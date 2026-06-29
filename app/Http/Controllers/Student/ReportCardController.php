<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\TableQueryRequest;
use App\Models\ReportCard;
use App\Services\StudentPanelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReportCardController extends Controller
{
    public function index(TableQueryRequest $request, StudentPanelService $panel): View
    {
        $studentId = (int) $request->user()->id;

        return view('pages.student.report-card.index', [
            'student' => $request->user(),
            'reportCards' => $panel->paginateReportCards($studentId, $request->validated()),
            'programOptions' => $panel->reportProgramOptions($studentId),
            'classOptions' => $panel->reportClassOptions($studentId),
            'instructorOptions' => $panel->reportInstructorOptions($studentId),
        ]);
    }

    public function show(Request $request, ReportCard $reportCard, StudentPanelService $panel): View
    {
        Gate::forUser($request->user())->authorize('view', $reportCard);

        $reportCard = $panel->ownedPublishedReportCard((int) $request->user()->id, $reportCard);

        return view('pages.student.report-card.show', [
            'student' => $request->user(),
            'reportCard' => $reportCard,
        ]);
    }
}
