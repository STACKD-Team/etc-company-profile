<?php

namespace App\Policies;

use App\Models\ReportCard;
use App\Models\User;

class ReportCardPolicy
{
    public function view(User $user, ReportCard $reportCard): bool
    {
        return $this->canAccessPublishedReport($user, $reportCard);
    }

    public function download(User $user, ReportCard $reportCard): bool
    {
        return $this->canAccessPublishedReport($user, $reportCard);
    }

    private function canAccessPublishedReport(User $user, ReportCard $reportCard): bool
    {
        if ($user->role !== 'student' || ! $reportCard->is_published) {
            return false;
        }

        return $reportCard->enrollment()
            ->where('user_id', $user->id)
            ->exists();
    }
}
