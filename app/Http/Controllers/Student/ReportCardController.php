<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ReportCard;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportCardController extends Controller
{
    public function index(Request $request): View
    {
        return view('student.report-cards.index', [
            'student' => $request->user(),
            'reportCards' => $request->user()->enrollments()
                ->with(['reportCard.instructor', 'courseClass.program', 'courseClass.instructor'])
                ->whereHas('reportCard', fn ($query) => $query->where('is_published', true))
                ->latest('enrolled_at')
                ->get()
                ->pluck('reportCard')
                ->filter(),
        ]);
    }

    public function show(Request $request, ReportCard $reportCard): View
    {
        $reportCard->load([
            'enrollment.courseClass.program',
            'enrollment.courseClass.instructor',
            'instructor',
            'academicDirector',
            'managingDirector',
        ]);

        abort_unless($reportCard->is_published && $reportCard->enrollment?->user_id === $request->user()->id, 403);

        return view('student.report-cards.show', [
            'student' => $request->user(),
            'reportCard' => $reportCard,
        ]);
    }
}
