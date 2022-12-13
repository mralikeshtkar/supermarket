<?php

namespace Modules\Order\Transformers\Api\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ApiOrderProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        //todo add discount
//        dd($this->resource->toArray());
        return collect([
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'price' => $this->resource->price,
        ])->when($this->resource->relationLoaded('image'), function (Collection $collection) {
            $collection->put('image', $this->resource->image);
        })->when($this->resource->relationLoaded('pivot'), function (Collection $collection) {
            $collection->put('pivot', $this->resource->pivot);
        })->toArray();
    }
}
