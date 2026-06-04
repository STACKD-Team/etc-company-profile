<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class RegistrationStartController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('registrations.programs.index');
    }
}
