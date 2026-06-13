<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\TableQueryRequest;
use App\Models\CourseClass;
use App\Services\StudentPanelService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(TableQueryRequest $request, StudentPanelService $panel): View
    {
        $studentId = (int) $request->user()->id;

        return view('pages.student.class.index', [
            'student' => $request->user(),
            'enrollments' => $panel->paginateClasses($studentId, $request->validated()),
            'programOptions' => $panel->programOptions($studentId),
            'classOptions' => $panel->classOptions($studentId),
            'instructorOptions' => $panel->instructorOptions($studentId),
            'statusLabels' => $panel->statusLabels(),
        ]);
    }

    public function show(Request $request, CourseClass $class, StudentPanelService $panel): View
    {
        $enrollment = $panel->ownedClassEnrollment((int) $request->user()->id, $class);

        return view('pages.student.class.show', [
            'student' => $request->user(),
            'enrollment' => $enrollment,
            'class' => $class->load(['program', 'instructor', 'room']),
        ]);
    }
}
