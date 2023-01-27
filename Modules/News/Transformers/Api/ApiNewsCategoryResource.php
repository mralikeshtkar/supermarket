<?php

namespace Modules\News\Transformers\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiNewsCategoryResource extends JsonResource
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
