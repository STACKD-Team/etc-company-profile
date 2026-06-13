<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use App\Services\PublicDiscoveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReelController extends Controller
{
    public function index(Request $request, PublicDiscoveryService $discovery): View
    {
        $reels = $discovery->reels();
        $selectedReelId = $request->integer('reel');

        if ($selectedReelId > 0 && $reels->contains('id', $selectedReelId)) {
            $reels = $reels
                ->sortByDesc(fn (Reel $reel): bool => $reel->getKey() === $selectedReelId)
                ->values();
        }

        return view('pages.public.reels.index', [
            'reels' => $reels,
            'likedReelIds' => collect($request->session()->get('liked_reels', []))
                ->map(fn (mixed $id): int => (int) $id)
                ->unique()
                ->values()
                ->all(),
        ]);
    }

    public function show(Reel $reel): RedirectResponse
    {
        abort_unless($reel->is_published, 404);

        return redirect()->route('public.reels.index', ['reel' => $reel->getKey()]);
    }
}
