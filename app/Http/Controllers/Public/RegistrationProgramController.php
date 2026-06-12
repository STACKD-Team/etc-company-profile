<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class RegistrationProgramController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('public.programs.index');
    }
}
