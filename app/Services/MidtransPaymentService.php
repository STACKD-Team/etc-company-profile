<?php

namespace App\Services;

use App\Models\MidtransNotification;
use App\Models\Program;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use RuntimeException;
use Throwable;

class MidtransPaymentService
{
    public function createTransaction(Registration $registration): Registration
    {
        $registration->loadMissing(['program', 'programPromotion']);
        $orderId = $registration->midtrans_order_id ?: $this->orderId($registration);
        $amount = (float) ($registration->final_amount ?: $registration->payment_amount ?: 0);

        $payload = $this->buildSnapPayload($registration, $orderId, $amount);

        if (! $this->isConfigured()) {
            return $this->storeDemoTransaction($registration, $orderId, 'Midtrans credentials are not configured; demo payment token generated.');
        }

        $this->configureSdk();
        try {
            $snap = Snap::createTransaction($payload);
        } catch (Throwable $exception) {
            if (app()->environment('testing')) {
                return $this->storeDemoTransaction($registration, $orderId, 'Midtrans sandbox call skipped during tests: '.$exception->getMessage());
            }

            throw $exception;
        }

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
     * @param array<string, mixed> $rawPayload
     */
    public function handleNotification(array $payload, array $rawPayload = []): MidtransNotification
    {
        return DB::transaction(function () use ($payload, $rawPayload): MidtransNotification {
            $orderId = (string) ($payload['order_id'] ?? '');
            $transactionStatus = (string) ($payload['transaction_status'] ?? 'unknown');
            $transactionId = (string) ($payload['transaction_id'] ?? $payload['order_id'] ?? '');

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
                    'raw_payload' => $rawPayload ?: $payload,
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
     * @param array<string, mixed> $returnPayload
     */
    public function syncFromReturn(Registration $registration, array $returnPayload): Registration
    {
        $orderId = (string) ($returnPayload['order_id'] ?? '');

        if ($orderId === '' || $orderId !== (string) $registration->midtrans_order_id) {
            return $registration->refresh();
        }

        if (! $this->isConfigured()) {
            return $registration->refresh();
        }

        $this->configureSdk();
        $statusPayload = (array) Transaction::status($orderId);

        if (! $this->validSignature($statusPayload)) {
            throw new RuntimeException('Invalid Midtrans status signature.');
        }

        $this->assertGrossAmountMatches($registration, $statusPayload);
        $this->applyStatus($registration, $statusPayload);

        return $registration->refresh();
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function validSignature(array $payload): bool
    {
        $signature = (string) ($payload['signature_key'] ?? '');

        if (! $this->isConfigured()) {
            return true;
        }

        if ($signature === '') {
            return false;
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
        $mappedPaymentStatus = $this->normalizedPaymentStatus($transactionStatus, $fraudStatus);

        if ($registration->payment_status === 'paid' && $mappedPaymentStatus !== 'paid') {
            $registration->update([
                'payment_status_message' => 'Ignored late Midtrans status: '.$transactionStatus,
            ]);

            return;
        }

        $data = [
            'payment_method' => $paymentType,
            'payment_amount' => $amount,
            'payment_gateway_id' => $payload['transaction_id'] ?? $registration->payment_gateway_id,
            'payment_status' => $mappedPaymentStatus,
            'payment_status_message' => $this->statusMessage($transactionStatus, $fraudStatus),
        ];

        if ($mappedPaymentStatus === 'paid') {
            $data['status'] = 'paid';
            $data['paid_at'] = $registration->paid_at ?? now();
        } elseif ($mappedPaymentStatus === 'expired' || $mappedPaymentStatus === 'cancelled') {
            $data['status'] = 'cancelled';
        } elseif ($mappedPaymentStatus === 'failed') {
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

        if ($transactionStatus === 'pending' || ($transactionStatus === 'capture' && $fraudStatus === 'challenge')) {
            return 'waiting_payment';
        }

        if ($transactionStatus === 'expire') {
            return 'expired';
        }

        if ($transactionStatus === 'cancel') {
            return 'cancelled';
        }

        if (in_array($transactionStatus, ['deny', 'failure'], true)) {
            return 'failed';
        }

        return 'waiting_payment';
    }

    protected function statusMessage(string $transactionStatus, string $fraudStatus = ''): ?string
    {
        if ($fraudStatus !== '') {
            return "Midtrans {$transactionStatus}; fraud status: {$fraudStatus}";
        }

        return match ($transactionStatus) {
            'settlement', 'capture' => 'Payment completed by Midtrans.',
            'pending' => 'Waiting for Midtrans payment completion.',
            'expire' => 'Midtrans payment expired.',
            'cancel' => 'Midtrans payment cancelled.',
            'deny', 'failure' => 'Midtrans payment failed.',
            default => 'Midtrans status: '.$transactionStatus,
        };
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

    protected function buildSnapPayload(Registration $registration, string $orderId, float $amount): array
    {
        return [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) round($amount),
            ],
            'callbacks' => [
                'finish' => $this->finishRedirectUrl($registration),
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
    }

    protected function finishRedirectUrl(Registration $registration): string
    {
        return route('registrations.confirmation.show', ['registration' => $registration], true);
    }

    protected function storeDemoTransaction(Registration $registration, string $orderId, string $message): Registration
    {
        $registration->update([
            'midtrans_order_id' => $orderId,
            'midtrans_snap_token' => 'demo-'.$orderId,
            'midtrans_redirect_url' => $this->finishRedirectUrl($registration),
            'payment_gateway_id' => $orderId,
            'payment_status' => 'waiting_payment',
            'payment_status_message' => $message,
            'payment_expires_at' => now()->addDay(),
        ]);

        return $registration->refresh();
    }
}
