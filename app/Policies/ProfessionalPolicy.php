<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Professional;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfessionalPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Professional $professional): bool
    {
        return $user->id === $professional->user_id;
    }

    public function update(User $user, Professional $professional): bool
    {
        return $user->id === $professional->user_id;
    }

    public function delete(User $user, Professional $professional): bool
    {
        return $user->id === $professional->user_id;
    }
}
