<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearningHistoryController extends Controller
{
    public function index(Request $request): View
    {
        return view('student.learning-history', [
            'student' => $request->user(),
            'enrollments' => $request->user()->enrollments()
                ->with(['courseClass.program', 'courseClass.instructor', 'reportCard'])
                ->latest('enrolled_at')
                ->get(),
        ]);
    }
}
