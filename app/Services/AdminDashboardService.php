<?php

namespace App\Services;

use App\Models\CourseClass;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AdminDashboardService
{
    /**
     * @return array<string, int|float>
     */
    public function summary(): array
    {
        return [
            'students_count' => User::query()->students()->count(),
            'new_registrations_count' => Registration::query()->whereDate('created_at', today())->count(),
            'paid_revenue' => (float) Registration::query()->whereNotNull('paid_at')->sum('payment_amount'),
            'active_classes_count' => CourseClass::query()->where('status', 'ongoing')->count(),
        ];
    }

    public function latestRegistrations(int $limit = 5): Collection
    {
        return Registration::query()
            ->with('program')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
