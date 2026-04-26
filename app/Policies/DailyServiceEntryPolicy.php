<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DailyServiceEntry;
use Illuminate\Auth\Access\HandlesAuthorization;

class DailyServiceEntryPolicy
{
    use HandlesAuthorization;

    public function view(User $user, DailyServiceEntry $entry): bool
    {
        return $user->id === $entry->user_id;
    }

    public function update(User $user, DailyServiceEntry $entry): bool
    {
        return $user->id === $entry->user_id;
    }

    public function delete(User $user, DailyServiceEntry $entry): bool
    {
        return $user->id === $entry->user_id;
    }
}
