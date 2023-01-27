<?php

namespace Modules\Order\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Permission\Enums\Permissions;
use Modules\User\Entities\User;

class OrderPolicy
{
    use HandlesAuthorization;

    public function manage(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_ORDERS);
    }

    public function show(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_ORDERS);
    }

    public function changeStatus(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_ORDERS);
    }

    public function deliveryDate(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_ORDERS);
    }

    public function factor(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_ORDERS);
    }
}
