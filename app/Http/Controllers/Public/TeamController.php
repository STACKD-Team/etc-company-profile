<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicDiscoveryService;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(PublicDiscoveryService $discovery): View
    {
        return view('pages.public.team.index', [
            'instructors' => $discovery->instructors(),
        ]);
    }
}
