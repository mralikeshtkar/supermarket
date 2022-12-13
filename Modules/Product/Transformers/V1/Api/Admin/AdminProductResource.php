<?php

namespace Modules\Product\Transformers\V1\Api\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use function collect;

class AdminProductResource extends JsonResource
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
            })->when($this->resource->relationLoaded('parentFeatures'), function (Collection $collection) {
                $collection->put('parentFeatures', $this->resource->parentFeatures);
            });
    }
}
