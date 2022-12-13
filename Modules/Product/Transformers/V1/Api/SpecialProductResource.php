<?php

namespace Modules\Product\Transformers\V1\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class SpecialProductResource extends JsonResource
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
            'priority' => $this->resource->priority,
            'product_id' => $this->resource->product_id,
        ])->when($this->resource->relationLoaded('product'), function (Collection $collection) {
            $collection->put('product', new ProductResource($this->resource->product));
        });
    }
}
