<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicDiscoveryService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(PublicDiscoveryService $discovery): View
    {
        return view('public.home', [
            'programs' => $discovery->programHighlights(),
            'partners' => $discovery->partners(),
            'reels' => $discovery->reels(4),
            'instructors' => $discovery->instructors(4),
            'settings' => $discovery->settings(),
            'stats' => $discovery->stats(),
            'faqs' => collect($discovery->faqItems())->take(5)->all(),
        ]);
    }
}
