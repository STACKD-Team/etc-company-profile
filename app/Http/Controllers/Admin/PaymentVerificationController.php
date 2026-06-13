<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectPaymentRequest;
use App\Http\Requests\Admin\VerifyPaymentRequest;
use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;

class PaymentVerificationController extends Controller
{
    public function __construct(private RegistrationService $registrationService) {}

    public function verify(VerifyPaymentRequest $request, Registration $payment): RedirectResponse
    {
        $validated = $request->validated();

        $this->registrationService->markAsPaid(
            $payment,
            isset($validated['payment_amount']) ? (float) $validated['payment_amount'] : null,
            $validated['payment_method'] ?? null,
            true,
        );

        return to_route('admin.payment.show', $payment)->with('status', 'Pembayaran berhasil diverifikasi.');
    }

    public function reject(RejectPaymentRequest $request, Registration $payment): RedirectResponse
    {
        $this->registrationService->reject($payment, $request->validated('notes'));

        return to_route('admin.payment.show', $payment)->with('status', 'Pembayaran ditolak dan catatan tersimpan.');
    }
}
