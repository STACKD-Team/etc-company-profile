<?php

namespace App\Services;

use App\Models\Enrollment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class EnrollmentService extends BaseCrudService
{
    protected function modelClass(): string
    {
        return Enrollment::class;
    }

    protected function defaultWith(): array
    {
        return ['user', 'courseClass.program'];
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->applySorting($this->query($filters), $filters, [
            'user_id',
            'class_id',
            'enrolled_at',
            'completed_at',
            'status',
            'created_at',
        ], 'enrolled_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function complete(Enrollment $enrollment, ?string $completedAt = null): Enrollment
    {
        /** @var Enrollment $enrollment */
        $enrollment = $this->update($enrollment, [
            'completed_at' => $completedAt ?? now()->toDateString(),
            'status' => 'completed',
        ]);

        return $enrollment;
    }

    public function drop(Enrollment $enrollment, ?string $completedAt = null): Enrollment
    {
        /** @var Enrollment $enrollment */
        $enrollment = $this->update($enrollment, [
            'completed_at' => $completedAt,
            'status' => 'dropped',
        ]);

        return $enrollment;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(function (Builder $query) use ($search): void {
                $query->whereHas('user', fn (Builder $query) => $query
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('full_name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%'))
                    ->orWhereHas('courseClass', fn (Builder $query) => $query
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhereHas('program', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%')));
            }))
            ->when($filters['user_id'] ?? null, fn (Builder $query, int|string $userId) => $query->where('user_id', $userId))
            ->when($filters['class_id'] ?? null, fn (Builder $query, int|string $classId) => $query->where('class_id', $classId))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['active'] ?? false, fn (Builder $query) => $query->where('status', 'active'))
            ->when($filters['completed'] ?? false, fn (Builder $query) => $query->where('status', 'completed'));
    }
}
