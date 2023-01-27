<?php

namespace Modules\Comment\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Permission\Enums\Permissions;
use Modules\User\Entities\User;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function manage(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_COMMENTS);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function show(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_COMMENTS);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_COMMENTS);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function destroy(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_COMMENTS);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function edit(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_COMMENTS);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function changeStatus(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_COMMENTS);
    }
}
