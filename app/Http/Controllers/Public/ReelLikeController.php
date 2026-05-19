<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use App\Services\ReelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReelLikeController extends Controller
{
    public function store(Request $request, Reel $reel, ReelService $reels): JsonResponse
    {
        abort_unless($reel->is_published, 404);

        $likedReels = collect($request->session()->get('liked_reels', []))
            ->map(fn ($id): int => (int) $id)
            ->all();
        $reelId = (int) $reel->getKey();

        if (in_array($reelId, $likedReels, true)) {
            $likedReels = array_values(array_diff($likedReels, [$reelId]));
            $reel = $reels->decrementLikes($reel);
            $liked = false;
        } else {
            $likedReels[] = $reelId;
            $reel = $reels->incrementLikes($reel);
            $liked = true;
        }

        $request->session()->put('liked_reels', $likedReels);

        return response()->json([
            'status' => 'ok',
            'liked' => $liked,
            'likes_count' => $reel->likes_count,
        ]);
    }
}
