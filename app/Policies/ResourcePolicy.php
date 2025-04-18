<?php

namespace App\Policies;

use App\Models\User;

class ResourcePolicy
{
    public function accessFinance(User $user)
    {
        return collect($user->office_groups)
            ->pluck('displayName')
            ->contains('Finance');
    }

    public function accessHR(User $user)
    {
        return collect($user->office_groups)
            ->pluck('displayName')
            ->contains('Human Resources');
    }
}
