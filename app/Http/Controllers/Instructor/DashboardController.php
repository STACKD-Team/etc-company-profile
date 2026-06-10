<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Services\InstructorPanelService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, InstructorPanelService $panel): View
    {
        return view('instructor.dashboard', [
            'instructor' => $request->user(),
            ...$panel->dashboard((int) $request->user()->id),
        ]);
    }
}
