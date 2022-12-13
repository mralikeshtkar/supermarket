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
    public function changeStatus(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_COMMENTS);
    }
}
