<?php

namespace Modules\Address\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Permission\Enums\Permissions;
use Modules\User\Entities\User;

class CityPolicy
{
    use HandlesAuthorization;

    public function store(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_CITIES);
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_CITIES);
    }

    public function destroy(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_CITIES);
    }
}
