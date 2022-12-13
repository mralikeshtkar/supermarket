<?php

namespace Modules\Address\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Address\Entities\Address;
use Modules\Permission\Enums\Permissions;
use Modules\User\Entities\User;

class AddressPolicy
{
    use HandlesAuthorization;

    public function update(User $user,Address $address): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_ADDRESSES) || $user->id == $address->user_id;
    }

    public function show(User $user,Address $address): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_ADDRESSES) || $user->id == $address->user_id;
    }

    public function destroy(User $user,Address $address): bool
    {
        return $user->hasPermissionTo(Permissions::MANAGE_ADDRESSES) || $user->id == $address->user_id;
    }
}
