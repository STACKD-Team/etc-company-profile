<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Models\ReportCard;
use App\Services\DocumentExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportCardExportController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.placement-test.export', [
            'title' => 'Export Rapor',
            'description' => 'Download rapor wajib memakai template DOC dari folder context.',
            'action' => route('admin.exports.report-cards.download'),
            'type' => 'report-cards',
            'classes' => CourseClass::query()->orderBy('name')->get(),
            'reportCards' => ReportCard::query()->with('enrollment.user')->latest()->get(),
        ]);
    }

    public function download(Request $request, DocumentExportService $documents): Response
    {
        $content = $documents->reportCardsDoc($request->only(['report_card_id', 'class_id', 'is_published', 'issued_from', 'issued_to']));

        return response($content, 200, [
            'Content-Type' => 'application/msword; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rapor-etc.doc"',
        ]);
    }
}
