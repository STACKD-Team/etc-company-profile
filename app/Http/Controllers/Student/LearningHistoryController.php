<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\TableQueryRequest;
use App\Services\StudentPanelService;
use Illuminate\View\View;

class LearningHistoryController extends Controller
{
    public function index(TableQueryRequest $request, StudentPanelService $panel): View
    {
        $studentId = (int) $request->user()->id;

        return view('pages.student.learning-history.index', [
            'student' => $request->user(),
            'enrollments' => $panel->paginateLearningHistory($studentId, $request->validated()),
            'programOptions' => $panel->programOptions($studentId),
            'classOptions' => $panel->classOptions($studentId),
            'instructorOptions' => $panel->instructorOptions($studentId),
            'statusLabels' => $panel->statusLabels(),
        ]);
    }
}
