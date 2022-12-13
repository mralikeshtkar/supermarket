<?php

namespace Modules\Rack\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Permission\Enums\Permissions;
use Modules\User\Entities\User;

class RackPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function manage(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_RACKS);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function store(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_RACKS);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_RACKS);
    }
}
