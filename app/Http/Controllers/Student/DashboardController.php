<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\ReportCard;
use App\Services\StudentPanelService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, StudentPanelService $panel): View
    {
        $student = $request->user();

        $activeEnrollments = $student->enrollments()
            ->with(['courseClass.program', 'courseClass.instructor'])
            ->where('status', 'active')
            ->latest('enrolled_at')
            ->get();

        $currentEnrollment = $activeEnrollments->first()
            ?? $student->enrollments()
                ->with(['courseClass.program', 'courseClass.instructor'])
                ->latest('enrolled_at')
                ->first();

        $publishedReports = ReportCard::query()
            ->with(['enrollment.courseClass.program'])
            ->where('is_published', true)
            ->whereHas('enrollment', fn ($query) => $query->where('user_id', $student->id))
            ->latest('issued_at')
            ->take(2)
            ->get();

        $payments = Registration::query()
            ->where('user_id', $student->id)
            ->whereNotNull('payment_amount')
            ->latest('paid_at')
            ->latest('created_at')
            ->get();
        $latestPayment = $payments->first();

        $recentLearningHistory = $student->enrollments()
            ->with(['courseClass.program', 'courseClass.instructor', 'reportCard'])
            ->latest('enrolled_at')
            ->take(3)
            ->get();

        $activeCourseClass = $currentEnrollment?->courseClass;
        $activeProgram = $activeCourseClass?->program;
        $durationMeetings = max((int) ($activeProgram?->duration_meetings ?? 0), 1);
        $completedMeetings = $currentEnrollment ? min(12, $durationMeetings) : 0;
        $progressPercent = $currentEnrollment ? (int) round(($completedMeetings / $durationMeetings) * 100) : 0;

        return view('student.dashboard', [
            'student' => $student,
            'activeEnrollments' => $activeEnrollments,
            'currentEnrollment' => $currentEnrollment,
            'activeCourseClass' => $activeCourseClass,
            'activeProgram' => $activeProgram,
            'publishedReports' => $publishedReports,
            'payments' => $payments,
            'latestPayment' => $latestPayment,
            'latestPaymentSummary' => $latestPayment ? $panel->paymentSummary($latestPayment) : null,
            'latestReport' => $publishedReports->first(),
            'recentLearningHistory' => $recentLearningHistory,
            'stats' => [
                'active_classes' => $activeEnrollments->count(),
                'total_meetings' => $activeEnrollments->sum(fn ($enrollment) => (int) ($enrollment->courseClass?->program?->duration_meetings ?? 0)),
                'average_grade' => $publishedReports->first()?->final_grade ?? '-',
                'certificates' => $publishedReports->count(),
            ],
            'courseProgress' => [
                'completed' => $completedMeetings,
                'total' => $durationMeetings,
                'percent' => $progressPercent,
            ],
        ]);
    }
}
