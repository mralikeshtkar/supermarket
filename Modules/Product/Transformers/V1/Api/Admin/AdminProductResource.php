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
            })->when($this->resource->originalIsEquivalent('status'), function (Collection $collection) {
                $collection->put('status', $this->resource->status)
                    ->put('translated_status', $this->resource->getTranslatedStatus())
                    ->put('status_css_class', $this->resource->getStatusClassName());
            })->when($this->resource->originalIsEquivalent('created_at'), function (Collection $collection) {
                $collection->put('created_at', verta($this->resource->created_at)->formatJalaliDate());
            });
    }
}
