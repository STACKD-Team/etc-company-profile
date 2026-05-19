<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(): View
    {
        return view('instructor.index', [
            'title' => 'Kelas Mengajar',
            'active' => 'classes',
            'items' => CourseClass::query()
                ->with('program')
                ->where('instructor_id', auth()->id())
                ->latest()
                ->paginate(10),
            'columns' => ['Kelas', 'Program', 'Jadwal', 'Status'],
            'rowView' => 'instructor.partials.class-row',
            'empty' => 'Belum ada kelas yang ditugaskan.',
        ]);
    }

    public function show(CourseClass $class): View
    {
        abort_unless((int) $class->instructor_id === (int) auth()->id(), 403);

        $class->load('program');

        return view('admin.rasky.detail', [
            'title' => 'Detail Kelas',
            'area' => 'instructor',
            'active' => 'classes',
            'heading' => $class->name,
            'description' => $class->program?->name ?? '-',
            'details' => [
                'Hari' => $class->schedule_days ?? '-',
                'Jam' => $class->schedule_time ?? '-',
                'Ruangan' => $class->room ?? '-',
                'Status' => $class->status ?? '-',
            ],
        ]);
    }
}
