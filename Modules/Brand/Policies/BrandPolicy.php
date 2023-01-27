<?php

namespace Modules\Brand\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Permission\Enums\Permissions;
use Modules\User\Entities\User;

class BrandPolicy
{
    use HandlesAuthorization;

    public function manage(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_BRANDS);
    }

    public function show(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_BRANDS);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_BRANDS);
    }

    public function edit(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_BRANDS);
    }

    public function destroy(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_BRANDS);
    }

    public function changeStatus(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_BRANDS);
    }
}
