<?php

namespace Modules\User\Traits;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Modules\User\Entities\User;

trait HasFavouritable
{
    use HasRelationships;

    /**
     * Users have this favouritable in them list.
     *
     * @return MorphToMany
     */
    public function favouriteUsers(): MorphToMany
    {
        return $this->morphToMany(User::class, 'favouritable', 'favourites')
            ->withTimestamps();
    }
}
