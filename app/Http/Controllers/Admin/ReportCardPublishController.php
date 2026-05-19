<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportCard;
use App\Services\ReportCardService;
use Illuminate\Http\RedirectResponse;

class ReportCardPublishController extends Controller
{
    public function store(ReportCard $reportCard, ReportCardService $reportCards): RedirectResponse
    {
        $reportCards->publish($reportCard);

        return redirect()->route('admin.report-cards.show', $reportCard)->with('status', 'Rapor sudah dipublish.');
    }
}
