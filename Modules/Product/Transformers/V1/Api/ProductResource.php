<?php

namespace Modules\Product\Transformers\V1\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Modules\Product\Entities\Product;

class ProductResource extends JsonResource
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
            ->when($this->resource->relationLoaded('image'), function (Collection $collection) {
                $collection->put('image', $this->resource->image);
            })->when($this->resource->originalIsEquivalent('rate_avg'), function (Collection $collection) {
                $collection->put('rate_avg', floatval($this->resource->rate_avg));
            })->when($this->resource->relationLoaded('model'), function (Collection $collection) {
                $collection->put('model', $this->resource->model);
            });
    }
}
