<?php

namespace Modules\Product\Transformers\V1\Api\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class AdminProductFeatureChildrenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return collect($this->resource)
            ->put('attribute_ids', $this->resource->attributes->pluck('option_id')->filter()->toArray())
            ->when($this->resource->relationLoaded('attributes'), function (Collection $collection) {
                $collection->put('attributes', AdminProductAttributeResource::collection($this->resource->attributes));
            });
    }
}
