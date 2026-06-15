<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlacementTestController extends Controller
{
    public function index(Request $request): View
    {
        $statuses = ['paid' => 'Paid', 'placement_test' => 'Placement Test', 'enrolled' => 'Enrolled'];
        $sortMap = [
            'created_at' => 'created_at',
            'registration_code' => 'registration_code',
            'applicant_name' => 'applicant_name',
            'status' => 'status',
            'placement_test_at' => 'placement_test_at',
        ];
        $sort = array_key_exists((string) $request->query('sort'), $sortMap) ? (string) $request->query('sort') : 'created_at';
        $direction = in_array($request->query('direction'), ['asc', 'desc'], true) ? (string) $request->query('direction') : 'desc';

        $items = Registration::query()
            ->with('program')
            ->whereIn('status', array_keys($statuses))
            ->when($request->filled('search'), fn (Builder $query) => $query->where(function (Builder $query) use ($request): void {
                $search = (string) $request->query('search');

                $query->where('registration_code', 'like', '%'.$search.'%')
                    ->orWhere('applicant_name', 'like', '%'.$search.'%')
                    ->orWhere('applicant_email', 'like', '%'.$search.'%');
            }))
            ->when($request->filled('status') && array_key_exists((string) $request->query('status'), $statuses), fn (Builder $query) => $query->where('status', $request->query('status')))
            ->orderBy($sortMap[$sort], $direction)
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.placement-test.index', [
            'title' => 'Placement Test',
            'active' => 'placement-test',
            'items' => $items,
            'columns' => [
                'registration_code' => ['label' => 'Kode', 'sortable' => true],
                'applicant_name' => ['label' => 'Nama', 'sortable' => true],
                'program' => 'Program',
                'status' => ['label' => 'Status', 'sortable' => true, 'filter' => ['type' => 'select', 'name' => 'status', 'options' => $statuses]],
                'placement_test_at' => ['label' => 'Jadwal', 'sortable' => true],
            ],
            'rowView' => 'pages.admin.placement-test.partials.placement-row',
            'empty' => 'Belum ada pendaftar yang masuk proses placement test.',
            'emptyDescription' => 'Pendaftar paid, placement test, atau enrolled akan tampil di sini.',
            'searchPlaceholder' => 'Cari kode, nama, atau email',
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

        return view('pages.admin.placement-test.detail', [
            'title' => 'Detail Placement Test',
            'active' => 'placement-test',
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
            'placementScheduleAction' => route('admin.placement-test.schedule', $registration),
            'placementResultAction' => route('admin.placement-test.result.store', $registration),
        ]);
    }
}
