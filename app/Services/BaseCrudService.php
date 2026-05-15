<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

abstract class BaseCrudService
{
    /**
     * @return class-string<Model>
     */
    abstract protected function modelClass(): string;

    /**
     * @return array<int, string>
     */
    protected function defaultWith(): array
    {
        return [];
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->query($filters)->paginate($perPage);
    }

    public function all(array $filters = []): Collection
    {
        return $this->query($filters)->get();
    }

    public function find(int|string $id): Model
    {
        return $this->baseQuery()->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return DB::transaction(fn () => $this->modelClass()::create($data));
    }

    public function update(Model $model, array $data): Model
    {
        return DB::transaction(function () use ($model, $data) {
            $model->update($data);

            return $model->refresh();
        });
    }

    public function delete(Model $model): bool
    {
        return DB::transaction(fn () => (bool) $model->delete());
    }

    public function restore(int|string $id): Model
    {
        return DB::transaction(function () use ($id) {
            $model = $this->trashedQuery()->findOrFail($id);

            if (! $this->supportsSoftDeletes()) {
                return $model;
            }

            $model->restore();

            return $model->refresh();
        });
    }

    public function forceDelete(Model $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($this->supportsSoftDeletes() && method_exists($model, 'forceDelete')) {
                return (bool) $model->forceDelete();
            }

            return (bool) $model->delete();
        });
    }

    protected function query(array $filters = []): Builder
    {
        return $this->applyFilters($this->baseQuery(), $filters);
    }

    protected function baseQuery(): Builder
    {
        return $this->modelClass()::query()->with($this->defaultWith());
    }

    protected function trashedQuery(): Builder
    {
        $query = $this->baseQuery();

        if ($this->supportsSoftDeletes()) {
            $query->withTrashed();
        }

        return $query;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query;
    }

    protected function whereLike(Builder $query, array $columns, ?string $search): Builder
    {
        if ($search === null || trim($search) === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($columns, $search) {
            foreach ($columns as $column) {
                $query->orWhere($column, 'like', '%'.$search.'%');
            }
        });
    }

    protected function whereDateRange(Builder $query, string $column, array $filters, string $fromKey = 'date_from', string $toKey = 'date_to'): Builder
    {
        return $query
            ->when($filters[$fromKey] ?? null, fn (Builder $query, string $date) => $query->whereDate($column, '>=', $date))
            ->when($filters[$toKey] ?? null, fn (Builder $query, string $date) => $query->whereDate($column, '<=', $date));
    }

    protected function supportsSoftDeletes(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this->modelClass()), true);
    }
}
