<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ReportCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportCardDownloadController extends Controller
{
    public function __invoke(Request $request, ReportCard $reportCard): StreamedResponse
    {
        $reportCard->load('enrollment');

        abort_unless($reportCard->is_published && $reportCard->enrollment?->user_id === $request->user()->id, 403);
        abort_unless($reportCard->pdf_path && Storage::exists($reportCard->pdf_path), 404);

        return Storage::download($reportCard->pdf_path);
    }
}
