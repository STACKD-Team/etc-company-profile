<?php

namespace App\Services;

use App\Models\Reel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ReelService extends BaseCrudService
{
    public function __construct(
        protected MediaStorageService $mediaStorage,
    ) {}

    protected function modelClass(): string
    {
        return Reel::class;
    }

    public function createWithMedia(array $data, UploadedFile $video, ?UploadedFile $thumbnail = null): Reel
    {
        $data['video_path'] = $this->mediaStorage->putUploadedFile($video, 'reels/videos');

        if ($thumbnail !== null) {
            $data['thumbnail_path'] = $this->mediaStorage->putUploadedFile($thumbnail, 'reels/thumbnails');
        }

        /** @var Reel $reel */
        $reel = $this->create($data);

        return $reel;
    }

    public function updateWithMedia(Reel $reel, array $data, ?UploadedFile $video = null, ?UploadedFile $thumbnail = null): Reel
    {
        if ($video !== null) {
            $data['video_path'] = $this->mediaStorage->replace($reel->video_path, $video, 'reels/videos');
        }

        if ($thumbnail !== null) {
            $data['thumbnail_path'] = $this->mediaStorage->replace($reel->thumbnail_path, $thumbnail, 'reels/thumbnails');
        }

        /** @var Reel $reel */
        $reel = $this->update($reel, $data);

        return $reel;
    }

    public function publish(Reel $reel): Reel
    {
        if (! $reel->video_path) {
            throw new RuntimeException('A reel must have a video before it can be published.');
        }

        /** @var Reel $reel */
        $reel = $this->update($reel, [
            'is_published' => true,
            'published_at' => $reel->published_at ?? now(),
        ]);

        return $reel;
    }

    public function unpublish(Reel $reel): Reel
    {
        /** @var Reel $reel */
        $reel = $this->update($reel, ['is_published' => false]);

        return $reel;
    }

    public function incrementViews(Reel $reel): Reel
    {
        return DB::transaction(function () use ($reel) {
            $reel->increment('views_count');

            return $reel->refresh();
        });
    }

    public function incrementLikes(Reel $reel): Reel
    {
        return DB::transaction(function () use ($reel) {
            $reel->increment('likes_count');

            return $reel->refresh();
        });
    }

    public function decrementLikes(Reel $reel): Reel
    {
        return DB::transaction(function () use ($reel) {
            $reel->refresh();

            if ((int) $reel->likes_count > 0) {
                $reel->decrement('likes_count');
            }

            return $reel->refresh();
        });
    }

    public function forceDelete(Model $model): bool
    {
        /** @var Reel $model */
        $videoPath = $model->video_path;
        $thumbnailPath = $model->thumbnail_path;
        $deleted = parent::forceDelete($model);

        if ($deleted) {
            $this->mediaStorage->delete($videoPath);
            $this->mediaStorage->delete($thumbnailPath);
        }

        return $deleted;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $this->whereLike($query, ['title'], $filters['search'] ?? null)
            ->when($filters['category'] ?? null, fn (Builder $query, string $category) => $query->where('category', $category))
            ->when(array_key_exists('is_published', $filters), fn (Builder $query) => $query->where('is_published', (bool) $filters['is_published']));
    }
}
