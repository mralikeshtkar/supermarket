<?php

namespace Modules\Category\Transformers\V1\Api\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class AdminCategoryResource extends JsonResource
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
            ->when($this->resource->relationLoaded('image'), function (Collection $collection) {
                $collection->put('image', $this->resource->image);
            })->when($this->resource->originalIsEquivalent('status'), function (Collection $collection) {
                $collection->put('status', $this->resource->status)
                    ->put('translated_status', $this->resource->getTranslatedStatus())
                    ->put('status_css_class', $this->resource->getStatusClassName());
            });
    }
}
