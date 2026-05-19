<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(Request $request): View
    {
        return view('student.classes.index', [
            'student' => $request->user(),
            'enrollments' => $request->user()->enrollments()
                ->with(['courseClass.program', 'courseClass.instructor', 'reportCard'])
                ->latest('enrolled_at')
                ->get(),
        ]);
    }

    public function show(Request $request, CourseClass $class): View
    {
        $enrollment = $request->user()->enrollments()
            ->with(['courseClass.program', 'courseClass.instructor', 'reportCard'])
            ->where('class_id', $class->id)
            ->firstOrFail();

        return view('student.classes.show', [
            'student' => $request->user(),
            'enrollment' => $enrollment,
            'class' => $class->load(['program', 'instructor']),
        ]);
    }
}
