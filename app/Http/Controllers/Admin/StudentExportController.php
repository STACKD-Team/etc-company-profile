<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Models\Program;
use App\Services\DocumentExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class StudentExportController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.placement-test.export', [
            'title' => 'Export Rekap Siswa',
            'description' => 'Export wajib memakai template XLSX dari folder context.',
            'action' => route('admin.exports.students.download'),
            'type' => 'students',
            'programs' => Program::query()->orderBy('name')->get(),
            'classes' => CourseClass::query()->orderBy('name')->get(),
        ]);
    }

    public function download(Request $request, DocumentExportService $documents): Response
    {
        $content = $documents->studentWorkbook($request->only(['year', 'status', 'program_id', 'class_id']));

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="rekap-siswa-etc.xlsx"',
        ]);
    }
}
