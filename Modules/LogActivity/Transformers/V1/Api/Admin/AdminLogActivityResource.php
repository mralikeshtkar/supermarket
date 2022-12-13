<?php

namespace Modules\LogActivity\Transformers\V1\Api\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class AdminLogActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return collect($this->resource)->when($this->resource->originalIsEquivalent('event'), function (Collection $collection) {
            $collection->put('event',trans($this->resource->event));
        })->when($this->resource->originalIsEquivalent('created_at'), function (Collection $collection) {
            $collection->put('created_at',jalaliFormat($this->resource->created));
        })->when($this->resource->originalIsEquivalent('subject_type'), function (Collection $collection) {
            $collection->put('subject_translated',trans($this->resource->subject_type));
        });
    }
}
