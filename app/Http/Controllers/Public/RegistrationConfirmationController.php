<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\URL;

class RegistrationConfirmationController extends Controller
{
    public function show(Registration $registration): View
    {
        $registration->loadMissing('program');

        return view('public.registration.confirmation', [
            'registration' => $registration,
            'receiptUrl' => URL::signedRoute('registrations.receipt.download', ['registration' => $registration]),
            'registrationDetail' => [
                'studentName' => $registration->applicant_name,
                'programName' => $registration->program?->name ?? '-',
                'registrationCode' => $registration->registration_code,
                'status' => $registration->status,
            ],
        ]);
    }
}
