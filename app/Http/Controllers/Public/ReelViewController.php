<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use App\Services\ReelService;
use Illuminate\Http\JsonResponse;

class ReelViewController extends Controller
{
    public function store(Reel $reel, ReelService $reels): JsonResponse
    {
        abort_unless($reel->is_published, 404);

        $reel = $reels->incrementViews($reel);

        return response()->json([
            'status' => 'ok',
            'views_count' => $reel->views_count,
        ]);
    }
}
