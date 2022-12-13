<?php

namespace Modules\Tag\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Permission\Enums\Permissions;
use Modules\User\Entities\User;

class TagPolicy
{
    use HandlesAuthorization;

    public function manage(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_TAGS);
    }

    public function store(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_TAGS);
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_TAGS);
    }

    public function destroy(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_TAGS);
    }
}
