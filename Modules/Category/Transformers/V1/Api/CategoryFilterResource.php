<?php

namespace Modules\Category\Transformers\V1\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryFilterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return collect($this->resource);
    }
}
