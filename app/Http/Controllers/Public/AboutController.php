<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicDiscoveryService;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(PublicDiscoveryService $discovery): View
    {
        return view('public.about', [
            'page' => $discovery->page('about'),
            'settings' => $discovery->settings(),
        ]);
    }
}
