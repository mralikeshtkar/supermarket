<?php

namespace Modules\Category\Traits;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Modules\Category\Entities\Category;

trait HasCategory
{
    use HasRelationships;

    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class,'categorizables');
    }

    public function categoriesWithoutParent(): MorphToMany
    {
        return $this->categories()->without('parent');
    }
}
