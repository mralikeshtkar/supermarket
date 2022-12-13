<?php

namespace Modules\Discount\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Permission\Enums\Permissions;
use Modules\User\Entities\User;

class DiscountPolicy
{
    use HandlesAuthorization;

    public function store(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_DISCOUNTS);
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_DISCOUNTS);
    }

    public function destroy(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_DISCOUNTS);
    }
}
