<?php

namespace Modules\User\Transformers\V1\Api\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class AdminUserOrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        //todo add discount
//        dd($this->resource->toArray());
        return collect([
            'name' => $this->resource->name,
            'id' => $this->resource->id,
            'price' => $this->resource->price,
        ])->when($this->resource->relationLoaded('model'), function (Collection $collection) {
            $collection->put('model', $this->resource->model);
        })->when($this->resource->relationLoaded('image'), function (Collection $collection) {
            $collection->put('image', $this->resource->image);
        })->when($this->resource->relationLoaded('pivot'), function (Collection $collection) {
            $collection->put('pivot', $this->resource->pivot);
        })->toArray();
    }
}
