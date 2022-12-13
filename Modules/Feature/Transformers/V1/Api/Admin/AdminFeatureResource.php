<?php

namespace Modules\Feature\Transformers\V1\Api\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class AdminFeatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return collect($this->resource->toArray())
            ->when($this->resource->relationLoaded('options'), function (Collection $collection) {
                $collection->put('options', $this->resource->options);
            });
    }
}
