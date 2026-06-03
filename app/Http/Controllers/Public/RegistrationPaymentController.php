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
                'total' => $this->rupiah((float) $registration->payment_amount),
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
            ->with('status', 'Konfirmasi pembayaran diterima. Admin ETC akan memverifikasi bukti pembayaran.');
    }

    protected function rupiah(float $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
