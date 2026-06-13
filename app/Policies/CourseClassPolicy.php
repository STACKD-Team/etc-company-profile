<?php

namespace App\Policies;

use App\Models\CourseClass;
use App\Models\User;

class CourseClassPolicy
{
    public function view(User $user, CourseClass $class): bool
    {
        if ($user->role !== 'student') {
            return false;
        }

        return $class->enrollments()
            ->where('user_id', $user->id)
            ->exists();
    }
}
