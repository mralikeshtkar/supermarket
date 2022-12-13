<?php

namespace Modules\Comment\Transformers\V1\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentUserResource extends JsonResource
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
