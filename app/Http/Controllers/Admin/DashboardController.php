<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(AdminDashboardService $dashboard): View
    {
        return view('admin.dashboard', [
            'summary' => $dashboard->summary(),
            'latestRegistrations' => $dashboard->latestRegistrations(),
        ]);
    }
}
