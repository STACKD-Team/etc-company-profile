<?php

namespace App\Services;

use App\Models\CourseClass;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class CourseClassService extends BaseCrudService
{
    protected function modelClass(): string
    {
        return CourseClass::class;
    }

    protected function defaultWith(): array
    {
        return ['program', 'instructor', 'room'];
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->applySorting($this->query($filters), $filters, [
            'name',
            'program_id',
            'instructor_id',
            'status',
            'start_date',
            'end_date',
            'created_at',
        ], 'start_date', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $hasLegacyRoomColumn = Schema::hasColumn('classes', 'room');

        $query
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(function (Builder $query) use ($search, $hasLegacyRoomColumn): void {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhereHas('room', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'))
                    ->orWhereHas('program', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'))
                    ->orWhereHas('instructor', fn (Builder $query) => $query
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('full_name', 'like', '%'.$search.'%'));

                if ($hasLegacyRoomColumn) {
                    $query->orWhere('room', 'like', '%'.$search.'%');
                }
            }))
            ->when($filters['program_id'] ?? null, fn (Builder $query, int|string $programId) => $query->where('program_id', $programId))
            ->when($filters['instructor_id'] ?? null, fn (Builder $query, int|string $instructorId) => $query->where('instructor_id', $instructorId))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status));

        $this->whereDateRange($query, 'start_date', $filters, 'start_from', 'start_to');
        $this->whereDateRange($query, 'end_date', $filters, 'end_from', 'end_to');

        return $query;
    }
}
