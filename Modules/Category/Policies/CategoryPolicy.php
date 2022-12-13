<?php

namespace Modules\Category\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Permission\Enums\Permissions;
use Modules\User\Entities\User;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function manage(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_CATEGORIES);
    }

    public function store(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_CATEGORIES);
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_CATEGORIES);
    }

    public function destroy(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_CATEGORIES);
    }

    public function action(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_CATEGORIES);
    }
}
