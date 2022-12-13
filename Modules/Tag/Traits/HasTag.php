<?php

namespace Modules\Tag\Traits;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Modules\Tag\Entities\Tag;

trait HasTag
{
    use HasRelationships;

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class,'taggable');
    }
}
