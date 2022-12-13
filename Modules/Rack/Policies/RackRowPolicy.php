<?php

namespace Modules\Rack\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Permission\Enums\Permissions;
use Modules\User\Entities\User;

class RackRowPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_RACK_ROWS);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_RACK_ROWS);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function destroy(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_RACK_ROWS);
    }
}
