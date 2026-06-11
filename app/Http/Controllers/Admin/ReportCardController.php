<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveReportCardRequest;
use App\Models\Enrollment;
use App\Models\ReportCard;
use App\Models\User;
use App\Services\DocumentExportService;
use App\Services\ReportCardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportCardController extends Controller
{
    public function index(Request $request, ReportCardService $reportCards): View
    {
        $filters = $request->only(['search', 'issued_from', 'issued_to', 'sort', 'direction']);

        if ($request->filled('is_published')) {
            $filters['is_published'] = $request->boolean('is_published');
        }

        return view('admin.rasky.index', [
            'title' => 'Rapor',
            'active' => 'reports',
            'items' => $reportCards->adminPaginate($filters, 10),
            'columns' => [
                'student' => 'Siswa',
                'class' => 'Kelas',
                'total_score' => ['label' => 'Total', 'sortable' => true],
                'is_published' => ['label' => 'Status', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'is_published', 'options' => ['1' => 'Published', '0' => 'Draft']]],
                'issued_at' => ['label' => 'Terbit', 'sortable' => true, 'filter' => ['type' => 'date', 'name' => 'issued_from']],
            ],
            'rowView' => 'admin.rasky.partials.report-card-row',
            'empty' => 'Belum ada rapor.',
            'emptyDescription' => 'Rapor akan tampil setelah dibuat dari enrollment siswa.',
            'searchPlaceholder' => 'Cari siswa atau kelas',
            'actions' => [
                ['label' => 'Buat Rapor', 'route' => 'admin.report-cards.create', 'icon' => 'add'],
                ['label' => 'Export Rapor', 'route' => 'admin.exports.report-cards', 'icon' => 'download'],
            ],
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

    public function store(SaveReportCardRequest $request, ReportCardService $reportCards): RedirectResponse
    {
        $reportCard = $reportCards->create($request->validated());

        return redirect()->route('admin.report-cards.show', $reportCard)->with('status', 'Rapor tersimpan.');
    }

    public function show(ReportCard $reportCard, DocumentExportService $documents): View
    {
        $reportCard->load('enrollment.user', 'enrollment.courseClass', 'instructor', 'academicDirector', 'managingDirector');

        return view('admin.rasky.report-card-preview', [
            'title' => 'Preview Rapor',
            'reportCard' => $reportCard,
            'documentHtml' => $documents->reportCardHtml($reportCard),
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

    public function update(SaveReportCardRequest $request, ReportCard $reportCard, ReportCardService $reportCards): RedirectResponse
    {
        $reportCards->update($reportCard, $request->validated());

        return redirect()->route('admin.report-cards.show', $reportCard)->with('status', 'Rapor diperbarui.');
    }
}
