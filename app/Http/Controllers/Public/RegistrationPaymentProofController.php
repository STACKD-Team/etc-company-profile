<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StorePaymentProofRequest;
use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;

class RegistrationPaymentProofController extends Controller
{
    public function store(
        StorePaymentProofRequest $request,
        Registration $registration,
        RegistrationService $registrations,
    ): RedirectResponse {
        $registrations->submitPaymentProof(
            $registration,
            $request->file('payment_proof'),
            $request->validated('payment_method'),
        );

        return back()->with('status', 'Bukti pembayaran berhasil diupload.');
    }
}
