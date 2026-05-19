<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\ReportCard;
use App\Models\User;
use App\Services\ReportCardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportCardController extends Controller
{
    public function index(ReportCardService $reportCards): View
    {
        return view('admin.rasky.index', [
            'title' => 'Rapor',
            'active' => 'reports',
            'items' => $reportCards->paginate([], 10),
            'columns' => ['Siswa', 'Kelas', 'Total', 'Status', 'Terbit'],
            'rowView' => 'admin.rasky.partials.report-card-row',
            'empty' => 'Belum ada rapor.',
        ]);
    }

    public function create(): View
    {
        return view('admin.rasky.report-card-form', [
            'title' => 'Buat Rapor',
            'reportCard' => new ReportCard,
            'enrollments' => Enrollment::query()->with('user', 'courseClass')->latest()->get(),
            'instructors' => User::query()->instructors()->orderBy('name')->get(),
            'directors' => User::query()->admins()->orderBy('name')->get(),
            'action' => route('admin.report-cards.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request, ReportCardService $reportCards): RedirectResponse
    {
        $reportCard = $reportCards->create($this->validatedData($request));

        return redirect()->route('admin.report-cards.show', $reportCard)->with('status', 'Rapor tersimpan.');
    }

    public function show(ReportCard $reportCard): View
    {
        $reportCard->load('enrollment.user', 'enrollment.courseClass', 'instructor', 'academicDirector', 'managingDirector');

        return view('admin.rasky.detail', [
            'title' => 'Preview Rapor',
            'active' => 'reports',
            'heading' => $reportCard->enrollment?->user?->full_name ?? $reportCard->enrollment?->user?->name ?? 'Rapor',
            'description' => $reportCard->enrollment?->courseClass?->name ?? '-',
            'details' => [
                'Total score' => $reportCard->total_score ?? '-',
                'Final grade' => $reportCard->final_grade ?? '-',
                'Next class' => $reportCard->next_class ?? '-',
                'Published' => $reportCard->is_published ? 'Ya' : 'Belum',
                'Issued at' => $reportCard->issued_at?->format('d M Y') ?? '-',
            ],
        ]);
    }

    public function edit(ReportCard $reportCard): View
    {
        return view('admin.rasky.report-card-form', [
            'title' => 'Edit Rapor',
            'reportCard' => $reportCard,
            'enrollments' => Enrollment::query()->with('user', 'courseClass')->latest()->get(),
            'instructors' => User::query()->instructors()->orderBy('name')->get(),
            'directors' => User::query()->admins()->orderBy('name')->get(),
            'action' => route('admin.report-cards.update', $reportCard),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, ReportCard $reportCard, ReportCardService $reportCards): RedirectResponse
    {
        $reportCards->update($reportCard, $this->validatedData($request, true));

        return redirect()->route('admin.report-cards.show', $reportCard)->with('status', 'Rapor diperbarui.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, bool $updating = false): array
    {
        return $request->validate([
            'enrollment_id' => [$updating ? 'sometimes' : 'required', 'integer', 'exists:enrollments,id'],
            'score_listening' => ['nullable', 'integer', 'min:0', 'max:20'],
            'score_vocabulary' => ['nullable', 'integer', 'min:0', 'max:20'],
            'score_structure' => ['nullable', 'integer', 'min:0', 'max:20'],
            'score_reading' => ['nullable', 'integer', 'min:0', 'max:20'],
            'score_writing' => ['nullable', 'integer', 'min:0', 'max:20'],
            'grade_pronunciation' => ['nullable', 'in:A,B,C,D'],
            'grade_sentence_arrangement' => ['nullable', 'in:A,B,C,D'],
            'grade_class_participation' => ['nullable', 'in:A,B,C,D'],
            'grade_questioning_skill' => ['nullable', 'in:A,B,C,D'],
            'grade_analyzing_skill' => ['nullable', 'in:A,B,C,D'],
            'total_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'final_grade' => ['nullable', 'in:A,B,C,D'],
            'next_class' => ['nullable', 'string', 'max:100'],
            'comments' => ['nullable', 'string'],
            'instructor_id' => ['nullable', 'integer', 'exists:users,id'],
            'academic_director_id' => ['nullable', 'integer', 'exists:users,id'],
            'managing_director_id' => ['nullable', 'integer', 'exists:users,id'],
            'issued_at' => ['nullable', 'date'],
        ]);
    }
}
