<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Models\Registration;
use Illuminate\View\View;

class PlacementTestController extends Controller
{
    public function index(): View
    {
        return view('admin.rasky.index', [
            'title' => 'Placement Test',
            'active' => 'placement',
            'items' => Registration::query()
                ->with('program')
                ->whereIn('status', ['paid', 'placement_test', 'enrolled'])
                ->latest()
                ->paginate(10),
            'columns' => ['Kode', 'Nama', 'Program', 'Status', 'Jadwal'],
            'rowView' => 'admin.rasky.partials.placement-row',
            'empty' => 'Belum ada pendaftar yang masuk proses placement test.',
        ]);
    }

    public function show(Registration $registration): View
    {
        $registration->load('program', 'courseClass');

        $classes = CourseClass::query()
            ->where('program_id', $registration->program_id)
            ->orderByRaw("case when status in ('upcoming', 'ongoing') then 0 else 1 end")
            ->orderBy('name')
            ->get();

        return view('admin.rasky.detail', [
            'title' => 'Detail Placement Test',
            'active' => 'placement',
            'heading' => $registration->registration_code,
            'description' => $registration->applicant_name,
            'details' => [
                'Program' => $registration->program?->name ?? '-',
                'Status' => str($registration->status)->replace('_', ' ')->headline()->toString(),
                'Jadwal placement' => $registration->placement_test_at?->format('d M Y H:i') ?? '-',
                'Hasil placement' => $registration->placement_test_result ?? '-',
                'Kelas' => $registration->courseClass?->name ?? '-',
            ],
            'placementRegistration' => $registration,
            'placementClasses' => $classes,
            'placementScheduleAction' => route('admin.placement-tests.schedule', $registration),
            'placementResultAction' => route('admin.placement-tests.result.store', $registration),
        ]);
    }
}
