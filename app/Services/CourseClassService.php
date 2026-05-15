<?php

namespace App\Services;

use App\Models\CourseClass;
use Illuminate\Database\Eloquent\Builder;

class CourseClassService extends BaseCrudService
{
    protected function modelClass(): string
    {
        return CourseClass::class;
    }

    protected function defaultWith(): array
    {
        return ['program', 'instructor'];
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $query
            ->when($filters['program_id'] ?? null, fn (Builder $query, int|string $programId) => $query->where('program_id', $programId))
            ->when($filters['instructor_id'] ?? null, fn (Builder $query, int|string $instructorId) => $query->where('instructor_id', $instructorId))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status));

        $this->whereDateRange($query, 'start_date', $filters, 'start_from', 'start_to');
        $this->whereDateRange($query, 'end_date', $filters, 'end_from', 'end_to');

        return $query;
    }
}
