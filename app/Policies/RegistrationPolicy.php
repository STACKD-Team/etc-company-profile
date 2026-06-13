<?php

namespace App\Policies;

use App\Models\Registration;
use App\Models\User;

class RegistrationPolicy
{
    public function view(User $user, Registration $registration): bool
    {
        return $user->role === 'student'
            && (int) $registration->user_id === (int) $user->id;
    }
}
