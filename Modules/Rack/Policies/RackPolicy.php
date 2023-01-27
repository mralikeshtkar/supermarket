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
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_RACKS);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function edit(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_RACKS);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function changeSort(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_RACKS);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function changeStatus(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_RACKS);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function changeSortRows(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_RACKS);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function destroy(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_RACKS);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function manageRackRows(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_RACKS);
    }
}
