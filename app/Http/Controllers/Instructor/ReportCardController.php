<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\ReportCard;
use Illuminate\View\View;

class ReportCardController extends Controller
{
    public function index(): View
    {
        return view('instructor.index', [
            'title' => 'Rapor',
            'active' => 'reports',
            'items' => ReportCard::query()
                ->with('enrollment.user', 'enrollment.courseClass')
                ->where(function ($query) {
                    $query
                        ->where('instructor_id', auth()->id())
                        ->orWhereHas('enrollment.courseClass', fn ($query) => $query->where('instructor_id', auth()->id()));
                })
                ->latest()
                ->paginate(10),
            'columns' => ['Siswa', 'Kelas', 'Total', 'Status'],
            'rowView' => 'instructor.partials.report-card-row',
            'empty' => 'Belum ada rapor terkait kelas instructor.',
        ]);
    }
}
