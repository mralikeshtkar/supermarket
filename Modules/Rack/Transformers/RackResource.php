<?php

namespace Modules\Rack\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Modules\Rack\Entities\Rack;

class RackResource extends JsonResource
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
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'url' => $this->resource->url,
            'priority' => intval($this->resource->priority),
        ])->when($this->resource->relationLoaded('rows'), function (Collection $collection) {
            $collection->put('rows', RackRowResource::collection($this->resource->rows));
        })->toArray();
    }
}
