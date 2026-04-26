<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Customer $customer): bool
    {
        return $user->id === $customer->user_id;
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->id === $customer->user_id;
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->id === $customer->user_id;
    }
}
