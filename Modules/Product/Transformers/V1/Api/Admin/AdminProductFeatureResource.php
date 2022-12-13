<?php

namespace Modules\Product\Transformers\V1\Api\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class AdminProductFeatureResource extends JsonResource
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
            ->when($this->resource->relationLoaded('children'), function (Collection $collection) {
                $collection->put('children', AdminProductFeatureChildrenResource::collection($this->resource->children));
            });
    }
}
