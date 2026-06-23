<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Services\MidtransPaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Throwable;

class RegistrationConfirmationController extends Controller
{
    public function show(Request $request, Registration $registration, MidtransPaymentService $payments): View
    {
        if ($request->filled('order_id')) {
            try {
                $registration = $payments->syncFromReturn($registration, $request->query());
            } catch (Throwable $exception) {
                Log::warning('Midtrans return status sync failed.', [
                    'registration_id' => $registration->id,
                    'order_id' => $request->query('order_id'),
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $registration->loadMissing('program');
        $paymentStatus = $registration->payment_status ?: match ($registration->status) {
            'paid', 'placement_test', 'enrolled' => 'paid',
            'cancelled' => 'expired',
            'rejected' => 'failed',
            default => 'waiting_payment',
        };
        $copy = $this->copyFor($paymentStatus);

        return view('public.registration.confirmation', [
            'registration' => $registration,
            'receiptUrl' => URL::signedRoute('registrations.receipt.download', ['registration' => $registration]),
            'confirmationCopy' => $copy,
            'registrationDetail' => [
                'studentName' => $registration->applicant_name,
                'programName' => $registration->program?->name ?? '-',
                'registrationCode' => $registration->registration_code,
                'amount' => 'Rp '.number_format((float) ($registration->final_amount ?: $registration->payment_amount), 0, ',', '.'),
                'paymentStatus' => $paymentStatus,
                'status' => $registration->status,
                'paidAt' => $registration->paid_at,
                'expiresAt' => $registration->payment_expires_at,
                'redirectUrl' => $registration->midtrans_redirect_url,
            ],
        ]);
    }

    /**
     * @return array{icon: string, title: string, body: string, tone: string, next: string}
     */
    protected function copyFor(string $paymentStatus): array
    {
        return match ($paymentStatus) {
            'paid' => [
                'icon' => 'check',
                'title' => 'Pembayaran Berhasil',
                'body' => 'Terima kasih. Pembayaran kamu sudah diterima melalui Midtrans dan pendaftaran siap masuk tahap placement test offline.',
                'tone' => 'success',
                'next' => 'Admin ETC Planet akan menghubungi kamu untuk jadwal placement test.',
            ],
            'expired', 'cancelled' => [
                'icon' => 'schedule',
                'title' => 'Pembayaran Kedaluwarsa',
                'body' => 'Sesi pembayaran Midtrans sudah berakhir atau dibatalkan. Silakan hubungi admin ETC Planet jika ingin dibuatkan transaksi baru.',
                'tone' => 'danger',
                'next' => 'Status pendaftaran belum bisa diproses sebelum transaksi baru berhasil dibayar.',
            ],
            'failed' => [
                'icon' => 'close',
                'title' => 'Pembayaran Gagal',
                'body' => 'Midtrans menandai transaksi ini gagal atau ditolak. Kamu bisa mengulang pembayaran dari halaman pembayaran jika tautan masih tersedia.',
                'tone' => 'danger',
                'next' => 'Jika saldo terpotong, hubungi admin dengan kode pendaftaran.',
            ],
            default => [
                'icon' => 'hourglass_empty',
                'title' => 'Menunggu Pembayaran',
                'body' => 'Pendaftaran sudah tersimpan. Selesaikan pembayaran melalui Midtrans agar admin bisa memproses placement test.',
                'tone' => 'warning',
                'next' => 'Status akan otomatis berubah setelah Midtrans mengirim notifikasi pembayaran.',
            ],
        };
    }
}
