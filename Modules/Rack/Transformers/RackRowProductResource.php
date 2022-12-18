<?php

namespace Modules\Rack\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Modules\Product\Entities\Product;

class RackRowProductResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->map(function ($item) {
            return collect($this->resource)->when($item->relationLoaded('pivot'), function (Collection $collection) use ($item) {
                $collection->put('pivot', collect($item->pivot)->put('priority', $this->additional['rack_row']->priority));
            })->when($item->relationLoaded('gallery'), function (Collection $collection) use ($item) {
                $collection->put('gallery', $item->gallery);
            })->when($item->relationLoaded('image'), function (Collection $collection) use ($item) {
                $collection->put('image', $item->image);
            })->when($item->relationLoaded('model'), function (Collection $collection) use ($item) {
                $collection->put('model', $item->model);
            })->toArray();
        });
    }

}
