<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('instructor.dashboard', [
            'classesCount' => CourseClass::query()->where('instructor_id', auth()->id())->count(),
        ]);
    }
}
