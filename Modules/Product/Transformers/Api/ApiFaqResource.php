<?php

namespace Modules\Product\Transformers\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ApiFaqResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return collect($this->resource)->when(array_key_exists('created_at', $this->resource->getAttributes()), function (Collection $collection) {
            $collection->put('created_at', verta($this->resource->created_at)->formatJalaliDate());
        })->when($this->resource->relationLoaded('replies'), function (Collection $collection) {
            $collection->put('replies', ApiFaqResource::collection($this->resource->replies));
        });
    }
}
