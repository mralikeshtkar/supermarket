<?php

namespace Modules\Feature\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Permission\Enums\Permissions;
use Modules\User\Entities\User;

class FeaturePolicy
{
    use HandlesAuthorization;

    public function manage(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_FEATURES);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_FEATURES);
    }

    public function edit(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_FEATURES);
    }

    public function show(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_FEATURES);
    }

    public function destroy(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_FEATURES);
    }
}
