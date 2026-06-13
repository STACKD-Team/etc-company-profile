<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ConfirmRegistrationPaymentRequest;
use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;

class RegistrationPaymentController extends Controller
{
    public function show(Registration $registration): View
    {
        $registration->loadMissing('program');

        return view('public.registration.payment', [
            'registration' => $registration,
            'paymentSummary' => [
                'program' => $registration->program?->name ?? '-',
                'registrationFee' => $this->rupiah((float) ($registration->program?->registration_fee ?? 0)),
                'programFee' => $this->rupiah((float) ($registration->program?->price ?? 0)),
                'originalAmount' => $this->rupiah((float) ($registration->original_amount ?: $registration->payment_amount)),
                'discountAmount' => $this->rupiah((float) ($registration->discount_amount ?: 0)),
                'finalAmount' => $this->rupiah((float) ($registration->final_amount ?: $registration->payment_amount)),
                'promotionTitle' => $registration->program_promotion_title,
                'status' => $registration->payment_status ?: $registration->status,
                'expiresAt' => $registration->payment_expires_at,
                'redirectUrl' => $registration->midtrans_redirect_url,
                'snapToken' => $registration->midtrans_snap_token,
            ],
            'bankAccount' => [
                'bank' => 'BCA',
                'number' => '123-456-7890',
                'holder' => 'ETC Planet',
            ],
        ]);
    }

    public function confirm(
        ConfirmRegistrationPaymentRequest $request,
        Registration $registration,
        RegistrationService $registrations,
    ): RedirectResponse {
        $registration = $registrations->confirmPaymentSubmission(
            $registration,
            $request->validated('payment_method'),
        );

        return redirect()
            ->to(URL::signedRoute('registrations.confirmation.show', ['registration' => $registration]))
            ->with('status', 'Konfirmasi pembayaran legacy diterima. Admin ETC akan memeriksa arsip pembayaran manual jika diperlukan.');
    }

    protected function rupiah(float $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
