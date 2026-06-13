<?php

namespace App\Services;

use App\Models\MidtransNotification;
use App\Models\Program;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use RuntimeException;
use Throwable;

class MidtransPaymentService
{
    public function createTransaction(Registration $registration): Registration
    {
        $registration->loadMissing(['program', 'programPromotion']);
        $orderId = $registration->midtrans_order_id ?: $this->orderId($registration);
        $amount = (float) ($registration->final_amount ?: $registration->payment_amount ?: 0);

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) round($amount),
            ],
            'customer_details' => [
                'first_name' => $registration->applicant_name,
                'email' => $registration->applicant_email,
                'phone' => $registration->applicant_phone,
            ],
            'item_details' => [
                [
                    'id' => 'REG-'.$registration->program_id,
                    'price' => (int) round($amount),
                    'quantity' => 1,
                    'name' => Str::limit($registration->program?->name ?? 'ETC Planet Registration', 45, ''),
                ],
            ],
        ];

        if (! $this->isConfigured()) {
            $registration->update([
                'midtrans_order_id' => $orderId,
                'midtrans_snap_token' => 'demo-'.$orderId,
                'midtrans_redirect_url' => route('registrations.payment.show', $registration),
                'payment_gateway_id' => $orderId,
                'payment_status' => 'waiting_payment',
                'payment_status_message' => 'Midtrans credentials are not configured; demo payment token generated.',
                'payment_expires_at' => now()->addDay(),
            ]);

            return $registration->refresh();
        }

        $this->configureSdk();
        $snap = Snap::createTransaction($payload);

        $registration->update([
            'midtrans_order_id' => $orderId,
            'midtrans_snap_token' => $snap->token ?? null,
            'midtrans_redirect_url' => $snap->redirect_url ?? null,
            'payment_gateway_id' => $orderId,
            'payment_status' => 'waiting_payment',
            'payment_status_message' => 'Waiting for Midtrans payment.',
            'payment_expires_at' => now()->addDay(),
        ]);

        return $registration->refresh();
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function handleNotification(array $payload): MidtransNotification
    {
        return DB::transaction(function () use ($payload): MidtransNotification {
            $orderId = (string) ($payload['order_id'] ?? '');
            $transactionStatus = (string) ($payload['transaction_status'] ?? 'unknown');
            $transactionId = (string) ($payload['transaction_id'] ?? '');

            if ($orderId === '') {
                throw new RuntimeException('Midtrans notification order_id is missing.');
            }

            $registration = Registration::query()
                ->where('midtrans_order_id', $orderId)
                ->orWhere('payment_gateway_id', $orderId)
                ->lockForUpdate()
                ->first();

            $notification = MidtransNotification::query()->firstOrCreate(
                [
                    'order_id' => $orderId,
                    'transaction_status' => $transactionStatus,
                    'transaction_id' => $transactionId ?: null,
                ],
                [
                    'registration_id' => $registration?->id,
                    'payment_type' => $payload['payment_type'] ?? null,
                    'fraud_status' => $payload['fraud_status'] ?? null,
                    'status_code' => $payload['status_code'] ?? null,
                    'gross_amount' => isset($payload['gross_amount']) ? (float) $payload['gross_amount'] : null,
                    'signature_key' => (string) ($payload['signature_key'] ?? ''),
                    'raw_payload' => $payload,
                    'processing_status' => 'received',
                    'received_at' => now(),
                ],
            );

            if (! $notification->wasRecentlyCreated) {
                return $notification->refresh();
            }

            try {
                if (! $this->validSignature($payload)) {
                    throw new RuntimeException('Invalid Midtrans signature.');
                }

                if (! $registration) {
                    throw new RuntimeException('Registration for Midtrans order was not found.');
                }

                $this->assertGrossAmountMatches($registration, $payload);
                $this->applyStatus($registration, $payload);

                $notification->update([
                    'registration_id' => $registration->id,
                    'processing_status' => 'processed',
                    'processed_at' => now(),
                ]);
            } catch (Throwable $exception) {
                $notification->update([
                    'processing_status' => 'failed',
                    'processed_at' => now(),
                    'error_message' => $exception->getMessage(),
                ]);
            }

            return $notification->refresh();
        });
    }

    public function snapshotAmount(Program $program): array
    {
        $promotion = $program->currentPromotion();
        $original = (float) $program->registration_fee + (float) $program->price;
        $discount = $promotion ? $promotion->discountAmount($program->price) : 0.0;

        return [
            'original_amount' => $original,
            'discount_amount' => $discount,
            'final_amount' => max(0.0, $original - $discount),
            'payment_amount' => $original,
            'program_promotion_id' => $promotion?->id,
            'program_promotion_title' => $promotion?->title,
        ];
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function validSignature(array $payload): bool
    {
        $signature = (string) ($payload['signature_key'] ?? '');

        if ($signature === '' || ! $this->isConfigured()) {
            return ! $this->isConfigured();
        }

        $expected = hash('sha512',
            (string) ($payload['order_id'] ?? '').
            (string) ($payload['status_code'] ?? '').
            (string) ($payload['gross_amount'] ?? '').
            (string) config('midtrans.server_key')
        );

        return hash_equals($expected, $signature);
    }

    protected function applyStatus(Registration $registration, array $payload): void
    {
        $transactionStatus = (string) ($payload['transaction_status'] ?? 'unknown');
        $fraudStatus = (string) ($payload['fraud_status'] ?? '');
        $paymentType = $payload['payment_type'] ?? $registration->payment_method;
        $amount = isset($payload['gross_amount']) ? (float) $payload['gross_amount'] : (float) $registration->payment_amount;
        $data = [
            'payment_method' => $paymentType,
            'payment_amount' => $amount,
            'payment_gateway_id' => $payload['transaction_id'] ?? $registration->payment_gateway_id,
            'payment_status' => $this->normalizedPaymentStatus($transactionStatus, $fraudStatus),
            'payment_status_message' => $fraudStatus ? "Fraud status: {$fraudStatus}" : null,
        ];

        if (in_array($transactionStatus, ['settlement', 'capture'], true) && ! in_array($fraudStatus, ['deny', 'challenge'], true)) {
            $data['status'] = 'paid';
            $data['paid_at'] = $registration->paid_at ?? now();
        } elseif ($transactionStatus === 'expire') {
            $data['status'] = 'cancelled';
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'failure'], true)) {
            $data['status'] = 'rejected';
        }

        $registration->update($data);
    }

    protected function assertGrossAmountMatches(Registration $registration, array $payload): void
    {
        if (! isset($payload['gross_amount'])) {
            return;
        }

        $expected = round((float) ($registration->final_amount ?: $registration->payment_amount ?: 0), 2);
        $actual = round((float) $payload['gross_amount'], 2);

        if (abs($expected - $actual) > 0.01) {
            throw new RuntimeException("Midtrans gross amount mismatch. Expected {$expected}, got {$actual}.");
        }
    }

    protected function normalizedPaymentStatus(string $transactionStatus, string $fraudStatus = ''): string
    {
        if (in_array($transactionStatus, ['settlement', 'capture'], true) && ! in_array($fraudStatus, ['deny', 'challenge'], true)) {
            return 'paid';
        }

        if ($transactionStatus === 'pending') {
            return 'waiting_payment';
        }

        if ($transactionStatus === 'expire') {
            return 'expired';
        }

        if (in_array($transactionStatus, ['cancel', 'deny', 'failure'], true)) {
            return 'failed';
        }

        return 'waiting_payment';
    }

    protected function configureSdk(): void
    {
        Config::$serverKey = (string) config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = (bool) config('midtrans.sanitize');
        Config::$is3ds = (bool) config('midtrans.3ds');
    }

    protected function isConfigured(): bool
    {
        return (bool) config('midtrans.server_key');
    }

    protected function orderId(Registration $registration): string
    {
        return 'ETC-'.$registration->registration_code.'-'.$registration->id;
    }
}
