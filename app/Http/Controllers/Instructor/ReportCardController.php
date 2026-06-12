<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\SaveAssessmentRequest;
use App\Http\Requests\Instructor\TableQueryRequest;
use App\Models\Enrollment;
use App\Models\ReportCard;
use App\Services\InstructorPanelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportCardController extends Controller
{
    public function index(TableQueryRequest $request, InstructorPanelService $panel): View
    {
        $instructorId = (int) $request->user()->id;

        return view('pages.instructor.report-card.index', [
            'assessments' => $panel->paginateAssessments($instructorId, $request->validated()),
            'classOptions' => $panel->classOptions($instructorId),
            'studentOptions' => $panel->studentOptions($instructorId),
        ]);
    }

    public function create(Request $request, Enrollment $enrollment, InstructorPanelService $panel): View
    {
        $enrollment = $panel->ownedEnrollment((int) $request->user()->id, $enrollment);
        abort_if($enrollment->reportCard, 409, 'Assessment untuk siswa ini sudah tersedia.');

        return view('pages.instructor.report-card.create', [
            'enrollment' => $enrollment,
            'reportCard' => new ReportCard,
        ]);
    }

    public function store(
        SaveAssessmentRequest $request,
        Enrollment $enrollment,
        InstructorPanelService $panel,
    ): RedirectResponse {
        $reportCard = $panel->createDraft((int) $request->user()->id, $enrollment, $request->validated());

        return redirect()
            ->route('instructor.report-cards.show', $reportCard)
            ->with('status', 'Draft assessment berhasil disimpan.');
    }

    public function show(Request $request, ReportCard $reportCard, InstructorPanelService $panel): View
    {
        $reportCard = $panel->ownedReportCard((int) $request->user()->id, $reportCard);

        return view('pages.instructor.report-card.show', [
            'reportCard' => $reportCard,
            'isComplete' => $panel->isAssessmentComplete($reportCard),
        ]);
    }

    public function edit(Request $request, ReportCard $reportCard, InstructorPanelService $panel): View
    {
        $reportCard = $panel->ownedReportCard((int) $request->user()->id, $reportCard);
        abort_if($reportCard->is_published, 403, 'Rapor yang sudah dipublish tidak dapat diubah instructor.');

        return view('pages.instructor.report-card.edit', [
            'enrollment' => $reportCard->enrollment,
            'reportCard' => $reportCard,
        ]);
    }

    public function update(
        SaveAssessmentRequest $request,
        ReportCard $reportCard,
        InstructorPanelService $panel,
    ): RedirectResponse {
        $reportCard = $panel->updateDraft((int) $request->user()->id, $reportCard, $request->validated());

        return redirect()
            ->route('instructor.report-cards.show', $reportCard)
            ->with('status', 'Draft assessment berhasil diperbarui.');
    }
}
