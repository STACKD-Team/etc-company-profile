<?php

namespace App\Services;

use App\Models\Content;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class ContentService extends BaseCrudService
{
    public function __construct(
        protected MediaStorageService $mediaStorage,
    ) {}

    protected function modelClass(): string
    {
        return Content::class;
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
