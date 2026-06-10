<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\TableQueryRequest;
use App\Models\CourseClass;
use App\Services\InstructorPanelService;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(TableQueryRequest $request, InstructorPanelService $panel): View
    {
        $instructorId = (int) $request->user()->id;

        return view('instructor.classes.index', [
            'classes' => $panel->paginateClasses($instructorId, $request->validated()),
            'programOptions' => $panel->programOptions($instructorId),
        ]);
    }

    public function show(
        TableQueryRequest $request,
        CourseClass $class,
        InstructorPanelService $panel,
    ): View {
        $instructorId = (int) $request->user()->id;
        $class = $panel->ownedClass($instructorId, $class);

        return view('instructor.classes.show', [
            'class' => $class,
            'students' => $panel->paginateClassStudents($instructorId, $class, $request->validated()),
            'studentOptions' => $panel->studentOptions($instructorId, $class->id),
        ]);
    }
}
