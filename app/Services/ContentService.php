<?php

namespace App\Services;

use App\Models\Content;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ContentService extends BaseCrudService
{
    public function __construct(
        protected MediaStorageService $mediaStorage,
    ) {}

    protected function modelClass(): string
    {
        return Content::class;
    }

    public function adminPaginate(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->query($filters)
            ->orderBy('type')
            ->orderBy('display_order')
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param array<int, string> $slugs
     * @return Collection<int, Content>
     */
    public function settings(array $slugs = []): Collection
    {
        return Content::query()
            ->where('type', 'setting')
            ->when($slugs !== [], fn (Builder $query) => $query->whereIn('slug', $slugs))
            ->get()
            ->keyBy('slug');
    }

    /**
     * @param array<string, string|null> $values
     * @param array<string, string> $labels
     */
    public function updateSettings(array $values, array $labels, ?UploadedFile $qrisImage = null): void
    {
        DB::transaction(function () use ($values, $labels, $qrisImage): void {
            foreach ($labels as $slug => $title) {
                $content = Content::query()->firstOrNew([
                    'type' => 'setting',
                    'slug' => $slug,
                ]);

                $content->title = $title;
                $content->body = null;
                $content->display_order = array_search($slug, array_keys($labels), true) ?: 0;
                $content->is_published = true;

                if ($slug === 'qris') {
                    $content->meta = ['value' => $values[$slug] ?? 'QRIS ETC Planet'];

                    if ($qrisImage !== null) {
                        $content->image = $content->exists
                            ? $this->mediaStorage->replace($content->image, $qrisImage, 'settings')
                            : $this->mediaStorage->putUploadedFile($qrisImage, 'settings');
                    }
                } else {
                    $content->meta = ['value' => $values[$slug] ?? null];
                }

                $content->save();
            }
        });
    }

    public function createWithMedia(array $data, ?UploadedFile $image = null, array $images = []): Content
    {
        if ($image !== null) {
            $data['image'] = $this->mediaStorage->putUploadedFile($image, 'contents/images');
        }

        if ($images !== []) {
            $data['images'] = $this->uploadImages($images);
        }

        /** @var Content $content */
        $content = $this->create($data);

        return $content;
    }

    public function updateWithMedia(Content $content, array $data, ?UploadedFile $image = null, array $images = []): Content
    {
        if ($image !== null) {
            $data['image'] = $this->mediaStorage->replace($content->image, $image, 'contents/images');
        }

        if ($images !== []) {
            $this->deleteImages($content->images ?? []);
            $data['images'] = $this->uploadImages($images);
        }

        /** @var Content $content */
        $content = $this->update($content, $data);

        return $content;
    }

    public function forceDelete(Model $model): bool
    {
        /** @var Content $model */
        $image = $model->image;
        $images = $model->images ?? [];
        $deleted = parent::forceDelete($model);

        if ($deleted) {
            $this->mediaStorage->delete($image);
            $this->deleteImages($images);
        }

        return $deleted;
    }

    /**
     * @param array<int, UploadedFile> $images
     * @return array<int, string>
     */
    protected function uploadImages(array $images): array
    {
        return array_map(
            fn (UploadedFile $image) => $this->mediaStorage->putUploadedFile($image, 'contents/gallery'),
            $images,
        );
    }

    /**
     * @param array<int, string> $images
     */
    protected function deleteImages(array $images): void
    {
        foreach ($images as $image) {
            $this->mediaStorage->delete($image);
        }
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $this->whereLike($query, ['title', 'slug'], $filters['search'] ?? null)
            ->when($filters['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', $type))
            ->when($filters['slug'] ?? null, fn (Builder $query, string $slug) => $query->where('slug', $slug))
            ->when(array_key_exists('is_published', $filters), fn (Builder $query) => $query->where('is_published', (bool) $filters['is_published']));
    }
}
