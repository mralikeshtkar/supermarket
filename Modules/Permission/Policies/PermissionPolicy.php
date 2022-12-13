<?php

namespace Modules\Permission\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Permission\Enums\Permissions;
use Modules\User\Entities\User;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function manage(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_PERMISSIONS);
    }
}
