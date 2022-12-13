<?php

namespace Modules\Product\Transformers\V1\Api\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductAttributeResource extends JsonResource
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
