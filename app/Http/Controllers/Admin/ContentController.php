<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreContentRequest;
use App\Http\Requests\Admin\UpdateContentRequest;
use App\Models\Content;
use App\Services\ContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContentController extends Controller
{
    private const TYPES = ['page', 'gallery', 'partner', 'room', 'team_member_extra', 'setting'];

    public function __construct(private ContentService $contents) {}

    public function index(Request $request): View
    {
        return view('admin.contents.index', [
            'contents' => $this->contents->adminPaginate($this->filters($request), 12),
            'types' => self::TYPES,
        ]);
    }

    public function create(): View
    {
        return view('admin.contents.create', [
            'content' => new Content(['type' => 'page', 'is_published' => true]),
            'types' => self::TYPES,
        ]);
    }

    public function store(StoreContentRequest $request): RedirectResponse
    {
        $this->contents->createWithMedia(
            $this->payload($request),
            $request->file('image'),
            $request->file('images', []),
        );

        return to_route('admin.contents.index')->with('status', 'Konten berhasil dibuat.');
    }

    public function edit(Content $content): View
    {
        return view('admin.contents.edit', [
            'content' => $content,
            'types' => self::TYPES,
        ]);
    }

    public function update(UpdateContentRequest $request, Content $content): RedirectResponse
    {
        $this->contents->updateWithMedia(
            $content,
            $this->payload($request),
            $request->file('image'),
            $request->file('images', []),
        );

        return to_route('admin.contents.index')->with('status', 'Konten berhasil diperbarui.');
    }

    private function payload(Request $request): array
    {
        $data = $request->validated();
        unset($data['image'], $data['images']);

        $data['slug'] = filled($data['slug'] ?? null) ? Str::slug($data['slug']) : Str::slug($data['title']);
        $data['display_order'] = $data['display_order'] ?? 0;
        $data['is_published'] = $request->boolean('is_published');
        $data['meta'] = collect($data['meta'] ?? [])
            ->filter(fn ($value): bool => filled($value))
            ->all();

        return $data;
    }

    private function filters(Request $request): array
    {
        $filters = $request->only(['search', 'type', 'sort', 'direction']);

        if ($request->filled('is_published')) {
            $filters['is_published'] = $request->boolean('is_published');
        }

        return $filters;
    }
}
