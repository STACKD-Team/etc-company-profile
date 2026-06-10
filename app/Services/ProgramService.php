<?php

namespace App\Services;

use App\Models\Program;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class ProgramService extends BaseCrudService
{
    public function __construct(
        protected MediaStorageService $mediaStorage,
    ) {}

    protected function modelClass(): string
    {
        return Program::class;
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->applySorting($this->query($filters), $filters, [
            'name',
            'category',
            'type',
            'target_age',
            'price',
            'registration_fee',
            'is_active',
            'created_at',
        ], 'name', 'asc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createWithThumbnail(array $data, ?UploadedFile $thumbnail = null): Program
    {
        if ($thumbnail !== null) {
            $data['thumbnail'] = $this->mediaStorage->putUploadedFile($thumbnail, 'programs/thumbnails');
        }

        /** @var Program $program */
        $program = $this->create($data);

        return $program;
    }

    public function updateWithThumbnail(Program $program, array $data, ?UploadedFile $thumbnail = null): Program
    {
        if ($thumbnail !== null) {
            $data['thumbnail'] = $this->mediaStorage->replace($program->thumbnail, $thumbnail, 'programs/thumbnails');
        }

        /** @var Program $program */
        $program = $this->update($program, $data);

        return $program;
    }

    public function forceDelete(Model $model): bool
    {
        /** @var Program $model */
        $thumbnail = $model->thumbnail;
        $deleted = parent::forceDelete($model);

        if ($deleted) {
            $this->mediaStorage->delete($thumbnail);
        }

        return $deleted;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $this->whereLike($query, ['name', 'slug'], $filters['search'] ?? null)
            ->when($filters['category'] ?? null, fn (Builder $query, string $category) => $query->where('category', $category))
            ->when($filters['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', $type))
            ->when($filters['target_age'] ?? null, fn (Builder $query, string $targetAge) => $query->where('target_age', $targetAge))
            ->when(array_key_exists('is_active', $filters), fn (Builder $query) => $query->where('is_active', (bool) $filters['is_active']));
    }
}
