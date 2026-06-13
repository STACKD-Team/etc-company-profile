<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(AdminDashboardService $dashboard): View
    {
        return view('pages.admin.dashboard.index', [
            'summary' => $dashboard->summary(),
            'latestRegistrations' => $dashboard->latestRegistrations(),
        ]);
    }
}
