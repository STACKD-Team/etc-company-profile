<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class RegistrationPaymentController extends Controller
{
    public function show(string $registration): View
    {
        return view('public.registration.payment', [
            'registrationReference' => $registration,
            'paymentSummary' => [
                'program' => 'General English - Reguler',
                'registrationFee' => 'Rp 200.000',
                'monthlyFee' => 'Rp 0',
                'total' => 'Rp 200.000',
            ],
            'bankAccount' => [
                'bank' => 'BCA',
                'number' => '123-456-7890',
                'holder' => 'ETC Planet',
            ],
        ]);
    }
}
