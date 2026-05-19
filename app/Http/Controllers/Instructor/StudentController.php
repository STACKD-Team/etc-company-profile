<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        return view('instructor.index', [
            'title' => 'Siswa',
            'active' => 'students',
            'items' => Enrollment::query()
                ->with('user', 'courseClass')
                ->whereHas('courseClass', fn ($query) => $query->where('instructor_id', auth()->id()))
                ->latest()
                ->paginate(10),
            'columns' => ['Siswa', 'Email', 'Kelas', 'Status'],
            'rowView' => 'instructor.partials.student-row',
            'empty' => 'Belum ada siswa dari kelas yang diajar.',
        ]);
    }
}
