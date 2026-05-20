<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class RegistrationConfirmationController extends Controller
{
    public function show(string $registration): View
    {
        return view('public.registration.confirmation', [
            'registrationReference' => $registration,
            'registrationDetail' => [
                'studentName' => 'Budi Santoso',
                'programName' => 'General English',
                'registrationCode' => 'ETC-2024-89012A',
            ],
        ]);
    }
}
