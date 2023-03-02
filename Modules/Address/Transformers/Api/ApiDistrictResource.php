<?php

namespace Modules\Address\Transformers\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiDistrictResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
