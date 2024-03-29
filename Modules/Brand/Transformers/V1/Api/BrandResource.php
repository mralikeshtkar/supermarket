<?php

namespace Modules\Brand\Transformers\V1\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Modules\Brand\Enums\BrandStatus;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return collect($this->resource)->when($this->resource->relationLoaded('image'), function (Collection $collection) {
            $collection->put('image', $this->resource->image);
        });
    }
}
