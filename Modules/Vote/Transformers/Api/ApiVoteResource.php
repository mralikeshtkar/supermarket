<?php

namespace Modules\Vote\Transformers\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiVoteResource extends JsonResource
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
