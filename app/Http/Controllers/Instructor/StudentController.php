<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\TableQueryRequest;
use App\Services\InstructorPanelService;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(TableQueryRequest $request, InstructorPanelService $panel): View
    {
        $instructorId = (int) $request->user()->id;

        return view('pages.instructor.student.index', [
            'students' => $panel->paginateStudents($instructorId, $request->validated()),
            'classOptions' => $panel->classOptions($instructorId),
            'studentOptions' => $panel->studentOptions($instructorId),
        ]);
    }
}
