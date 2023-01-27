<?php

namespace Modules\Discount\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Permission\Enums\Permissions;
use Modules\User\Entities\User;

class DiscountPolicy
{
    use HandlesAuthorization;

    public function manage(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_DISCOUNTS);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_DISCOUNTS);
    }

    public function edit(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_DISCOUNTS);
    }

    public function destroy(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_DISCOUNTS);
    }

    public function changeStatus(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_DISCOUNTS);
    }
}
