<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CashRegister;
use Illuminate\Auth\Access\HandlesAuthorization;

class CashRegisterPolicy
{
    use HandlesAuthorization;

    public function view(User $user, CashRegister $cashRegister): bool
    {
        return $user->id === $cashRegister->user_id;
    }

    public function update(User $user, CashRegister $cashRegister): bool
    {
        return $user->id === $cashRegister->user_id;
    }

    public function delete(User $user, CashRegister $cashRegister): bool
    {
        return $user->id === $cashRegister->user_id;
    }
}
