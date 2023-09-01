<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isFrontdesk();
    }

    public function checkIn(User $user): bool
    {
        return $user->isFrontdesk();
    }

    public function checkOut(User $user): bool
    {
        return $user->isFrontdesk();
    }
}
