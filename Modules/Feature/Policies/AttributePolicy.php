<?php

namespace Modules\Feature\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Permission\Enums\Permissions;
use Modules\User\Entities\User;

class AttributePolicy
{
    use HandlesAuthorization;

    public function store(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_ATTRIBUTES);
    }
}
