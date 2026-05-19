<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicDiscoveryService;
use Illuminate\View\View;

class FacilityController extends Controller
{
    public function index(PublicDiscoveryService $discovery): View
    {
        return view('public.facilities.index', [
            'rooms' => $discovery->rooms(),
        ]);
    }
}
