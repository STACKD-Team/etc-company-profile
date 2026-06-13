<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ReportCard;
use App\Services\MediaStorageService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportCardDownloadController extends Controller
{
    public function __invoke(Request $request, ReportCard $reportCard, MediaStorageService $mediaStorage): StreamedResponse|BinaryFileResponse|RedirectResponse
    {
        Gate::forUser($request->user())->authorize('download', $reportCard);

        $reportCard->load('enrollment');

        abort_unless($reportCard->is_published && $reportCard->enrollment?->user_id === $request->user()->id, 403);
        abort_unless($reportCard->pdf_path, 404);

        if (str_starts_with($reportCard->pdf_path, 'cloudinary://')) {
            return redirect()->away($mediaStorage->url($reportCard->pdf_path));
        }

        abort_unless(Storage::exists($reportCard->pdf_path), 404);

        return Storage::download($reportCard->pdf_path);
    }
}
