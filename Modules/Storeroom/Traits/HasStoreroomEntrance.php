<?php

namespace Modules\Storeroom\Traits;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Modules\Product\Entities\Product;

trait HasStoreroomEntrance
{
    use HasRelationships;

    public function storeroom_entrances()
    {
        return $this->morphToMany(Product::class,'entrances','entrances');
    }
}
