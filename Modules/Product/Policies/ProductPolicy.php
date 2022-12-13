<?php

namespace Modules\Product\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Permission\Enums\Permissions;
use Modules\User\Entities\User;

class ProductPolicy
{
    use HandlesAuthorization;

    public function manage(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_PRODUCTS);
    }

    public function store(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_PRODUCTS);
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_PRODUCTS);
    }

    public function destroy(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_PRODUCTS);
    }

    public function gallery(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_PRODUCTS);
    }

    public function model(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_PRODUCTS);
    }
}
