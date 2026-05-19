<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportCardExportController extends Controller
{
    public function index(): View
    {
        return view('admin.rasky.export', [
            'title' => 'Export Rapor',
            'description' => 'Download rapor wajib memakai template DOC dari folder context.',
            'action' => route('admin.exports.report-cards.download'),
        ]);
    }

    public function download(): Response
    {
        return response('Template-based DOC export belum diaktifkan.', 501);
    }
}
