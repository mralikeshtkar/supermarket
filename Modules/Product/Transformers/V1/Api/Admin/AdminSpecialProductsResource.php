<?php

namespace Modules\Product\Transformers\V1\Api\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use function collect;

class AdminSpecialProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return collect([
            'id' => $this->resource->id,
            'product_id' => $this->resource->product_id,
            'priority' => $this->resource->priority,
        ])->when($this->resource->relationLoaded('product'), function (Collection $collection) {
            $collection->put('product', new AdminProductResource($this->resource->product));
        });
    }
}
