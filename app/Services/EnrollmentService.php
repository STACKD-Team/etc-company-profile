<?php

namespace App\Services;

use App\Models\Enrollment;
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
            ->when($filters['user_id'] ?? null, fn (Builder $query, int|string $userId) => $query->where('user_id', $userId))
            ->when($filters['class_id'] ?? null, fn (Builder $query, int|string $classId) => $query->where('class_id', $classId))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['active'] ?? false, fn (Builder $query) => $query->where('status', 'active'))
            ->when($filters['completed'] ?? false, fn (Builder $query) => $query->where('status', 'completed'));
    }
}
