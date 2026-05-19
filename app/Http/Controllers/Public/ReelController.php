<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use App\Services\PublicDiscoveryService;
use Illuminate\View\View;

class ReelController extends Controller
{
    public function index(PublicDiscoveryService $discovery): View
    {
        return view('public.reels.index', [
            'reels' => $discovery->reels(),
        ]);
    }

    public function show(Reel $reel): View
    {
        abort_unless($reel->is_published, 404);

        return view('public.reels.show', [
            'reel' => $reel,
            'liked' => in_array($reel->getKey(), session('liked_reels', []), true),
        ]);
    }
}
