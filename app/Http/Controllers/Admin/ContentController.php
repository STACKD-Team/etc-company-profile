<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DestroyAdminResourceRequest;
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
    private const TYPES = \App\Models\Content::TYPES;

    public function __construct(private ContentService $contents) {}

    public function index(Request $request, ?string $contentType = null): View
    {
        $contentType = $this->contentType($request, $contentType);

        return view($this->viewName($contentType, 'index'), [
            'contents' => $this->contents->adminPaginate($this->filters($request), 12),
            'types' => self::TYPES,
            ...$this->viewData($contentType),
        ]);
    }

    public function create(Request $request, ?string $contentType = null): View
    {
        $contentType = $this->contentType($request, $contentType);

        return view($this->viewName($contentType, 'create'), [
            'content' => new Content(['type' => $contentType, 'is_published' => true]),
            'types' => self::TYPES,
            ...$this->viewData($contentType),
        ]);
    }

    public function store(StoreContentRequest $request, ?string $contentType = null): RedirectResponse
    {
        $contentType = $this->contentType($request, $contentType);

        $content = $this->contents->createWithMedia(
            $this->payload($request, $contentType),
            $request->file('image'),
            $request->file('images', []),
        );

        return to_route('admin.'.$contentType.'.show', $content)->with('status', $this->label($contentType).' berhasil dibuat.');
    }

    public function show(Request $request, Content $content, ?string $contentType = null): View
    {
        $contentType = $this->contentType($request, $contentType);
        abort_unless($content->type === $contentType, 404);

        return view($this->viewName($contentType, 'show'), [
            'content' => $content,
            'types' => self::TYPES,
            ...$this->viewData($contentType),
        ]);
    }

    public function edit(Request $request, Content $content, ?string $contentType = null): View
    {
        $contentType = $this->contentType($request, $contentType);
        abort_unless($content->type === $contentType, 404);

        return view($this->viewName($contentType, 'edit'), [
            'content' => $content,
            'types' => self::TYPES,
            ...$this->viewData($contentType),
        ]);
    }

    public function update(UpdateContentRequest $request, Content $content, ?string $contentType = null): RedirectResponse
    {
        $contentType = $this->contentType($request, $contentType);
        abort_unless($content->type === $contentType, 404);

        $this->contents->updateWithMedia(
            $content,
            $this->payload($request, $contentType),
            $request->file('image'),
            $request->file('images', []),
        );

        return to_route('admin.'.$contentType.'.show', $content)->with('status', $this->label($contentType).' berhasil diperbarui.');
    }

    public function destroy(DestroyAdminResourceRequest $request, Content $content, ?string $contentType = null): RedirectResponse
    {
        $contentType = $this->contentType($request, $contentType);
        abort_unless($content->type === $contentType, 404);

        $request->validated();
        $this->contents->delete($content);

        return to_route('admin.'.$contentType.'.index')->with('status', $this->label($contentType).' berhasil dihapus.');
    }

    private function payload(Request $request, string $contentType): array
    {
        $data = $request->validated();
        unset($data['image'], $data['images']);

        $data['type'] = $contentType;
        $data['slug'] = Str::slug($data['title']);
        $data['display_order'] = $data['display_order'] ?? 0;
        $data['is_published'] = $request->boolean('is_published');
        $data['meta'] = $this->metaPayload($contentType, $data['meta'] ?? []);

        return $data;
    }

    private function filters(Request $request): array
    {
        $filters = $request->only(['search', 'type', 'sort', 'direction']);
        $filters['type'] = $this->contentType($request, $filters['type'] ?? null);

        if ($request->filled('is_published')) {
            $filters['is_published'] = $request->boolean('is_published');
        }

        return $filters;
    }

    private function contentType(Request $request, ?string $contentType = null): string
    {
        $contentType = $contentType ?: $request->route('contentType') ?: Content::TYPE_GALLERY;
        abort_unless(in_array($contentType, [Content::TYPE_GALLERY, Content::TYPE_PARTNER, Content::TYPE_TESTIMONIAL, Content::TYPE_FAQ], true), 404);

        return $contentType;
    }

    private function viewData(string $contentType): array
    {
        return [
            'contentType' => $contentType,
            'pageTitle' => $this->label($contentType),
            'routeBase' => 'admin.'.$contentType,
            'viewBase' => 'pages.admin.'.$contentType,
            'rowView' => 'pages.admin.'.$contentType.'.partials.row',
            'metaFields' => $this->metaFields($contentType),
        ];
    }

    private function viewName(string $contentType, string $page): string
    {
        return 'pages.admin.'.$contentType.'.'.$page;
    }

    private function label(string $contentType): string
    {
        return match ($contentType) {
            Content::TYPE_PARTNER => 'Partner',
            Content::TYPE_TESTIMONIAL => 'Testimonial',
            Content::TYPE_FAQ => 'FAQ',
            default => 'Gallery',
        };
    }

    private function metaFields(string $contentType): array
    {
        return match ($contentType) {
            Content::TYPE_PARTNER => [
                'category' => 'Kategori',
                'website' => 'Link Website',
                'since' => 'Tahun Kerja Sama',
            ],
            Content::TYPE_TESTIMONIAL => [
                'role' => 'Role',
                'rating' => 'Rating',
            ],
            default => [],
        };
    }

    /**
     * @param array<string, mixed> $meta
     * @return array<string, mixed>
     */
    private function metaPayload(string $contentType, array $meta): array
    {
        $allowed = match ($contentType) {
            Content::TYPE_GALLERY => ['caption', 'alt_text', 'category', 'event_date', 'location'],
            Content::TYPE_PARTNER => ['category', 'website', 'since'],
            Content::TYPE_TESTIMONIAL => ['role', 'rating'],
            default => [],
        };

        return collect($meta)
            ->only($allowed)
            ->filter(fn ($value): bool => filled($value))
            ->map(fn ($value, string $key): mixed => $key === 'rating' ? (int) $value : $value)
            ->all();
    }
}
