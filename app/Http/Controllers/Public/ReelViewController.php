<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use App\Services\ReelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReelViewController extends Controller
{
    public function store(Request $request, Reel $reel, ReelService $reels): JsonResponse
    {
        abort_unless($reel->is_published, 404);

        $viewedReels = collect($request->session()->get('viewed_reels', []))
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
        $reelId = (int) $reel->getKey();

        if (in_array($reelId, $viewedReels, true)) {
            return response()->json([
                'status' => 'ok',
                'counted' => false,
                'views_count' => (int) $reel->views_count,
            ]);
        }

        $reel = $reels->incrementViews($reel);
        $viewedReels[] = $reelId;
        $request->session()->put('viewed_reels', array_values(array_unique($viewedReels)));

        return response()->json([
            'status' => 'ok',
            'counted' => true,
            'views_count' => $reel->views_count,
        ]);
    }
}
