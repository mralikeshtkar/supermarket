<?php

namespace Modules\User\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Permission\Enums\Permissions;
use Modules\User\Entities\User;

class UserPolicy
{
    use HandlesAuthorization;

    public function manage(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_USERS);
    }

    public function show(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_USERS);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_USERS);
    }

    public function edit(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_USERS);
    }

    public function destroy(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_FEATURES);
    }
}
