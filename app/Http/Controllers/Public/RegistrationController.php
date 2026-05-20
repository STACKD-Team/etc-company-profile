<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class RegistrationController extends Controller
{
    public function create(?string $program = null): View
    {
        return view('public.registration.create', [
            'programSlug' => $program,
            'registrationReference' => 'demo',
            'selectedProgram' => 'Intensive English Preparation Course (IELTS)',
            'selectedSchedule' => 'Senin & Rabu, 16:00 WIB',
            'fees' => [
                'registration' => 'Rp 200.000',
                'program' => 'Rp 850.000',
                'total' => 'Rp 1.050.000',
            ],
        ]);
    }
}
