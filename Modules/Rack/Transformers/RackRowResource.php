<?php

namespace Modules\Rack\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class RackRowResource extends JsonResource
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
            'id' => $this->resource->id,
            'rack_id' => $this->resource->rack_id,
            'title' => $this->resource->title,
            'number_limit' => $this->resource->number_limit,
            'priority' => $this->resource->priority,
        ])->when($this->resource->relationLoaded('products'), function (Collection $collection) {
            $collection->put('products', RackRowProductResource::make($this->resource->products)->additional(['rack_row'=>$this->resource]));
        })->toArray();
    }
}
