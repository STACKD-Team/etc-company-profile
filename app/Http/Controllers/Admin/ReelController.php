<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreReelRequest;
use App\Http\Requests\Admin\UpdateReelRequest;
use App\Models\Reel;
use App\Services\ReelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReelController extends Controller
{
    private const CATEGORIES = ['promosi', 'dokumentasi', 'edukasi', 'testimoni', 'event'];

    public function __construct(private ReelService $reels) {}

    public function index(Request $request): View
    {
        return view('admin.reels.index', [
            'reels' => $this->reels->adminPaginate($this->filters($request), 12),
            'categories' => self::CATEGORIES,
        ]);
    }

    public function create(): View
    {
        return view('admin.reels.create', [
            'reel' => new Reel(['category' => 'edukasi']),
            'categories' => self::CATEGORIES,
        ]);
    }

    public function store(StoreReelRequest $request): RedirectResponse
    {
        $data = $this->payload($request);

        if ($data['is_published']) {
            $data['published_at'] = now();
        }

        $this->reels->createWithMedia($data, $request->file('video'), $request->file('thumbnail'));

        return to_route('admin.reels.index')->with('status', 'Reel berhasil diupload.');
    }

    public function edit(Reel $reel): View
    {
        return view('admin.reels.edit', [
            'reel' => $reel,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function update(UpdateReelRequest $request, Reel $reel): RedirectResponse
    {
        $data = $this->payload($request);
        $data['published_at'] = $data['is_published'] ? ($reel->published_at ?? now()) : null;

        $this->reels->updateWithMedia($reel, $data, $request->file('video'), $request->file('thumbnail'));

        return to_route('admin.reels.index')->with('status', 'Reel berhasil diperbarui.');
    }

    private function payload(Request $request): array
    {
        $data = $request->validated();
        unset($data['video'], $data['thumbnail']);

        $data['is_published'] = $request->boolean('is_published');
        $data['category'] ??= 'edukasi';
        $data['duration_seconds'] = $data['duration_seconds'] ?? null;

        return $data;
    }

    private function filters(Request $request): array
    {
        $filters = $request->only(['search', 'category']);

        if ($request->filled('is_published')) {
            $filters['is_published'] = $request->boolean('is_published');
        }

        return $filters;
    }
}
