<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;

class StudentExportController extends Controller
{
    public function index(): View
    {
        return view('admin.rasky.export', [
            'title' => 'Export Rekap Siswa',
            'description' => 'Export wajib memakai template XLSX dari folder context.',
            'action' => route('admin.exports.students.download'),
        ]);
    }

    public function download(): Response
    {
        return response('Template-based XLSX export belum diaktifkan.', 501);
    }
}
