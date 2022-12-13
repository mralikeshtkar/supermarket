<?php

namespace Modules\Comment\Transformers\V1\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Modules\Comment\Entities\Comment;

class CommentResource extends JsonResource
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
            ->when($this->resource->originalIsEquivalent('created_at'), function (Collection $collection) {
                $collection->put('created_at', verta($this->resource->created_at)->formatJalaliDate());
            })->when($this->resource->relationLoaded('user'), function (Collection $collection) {
                $collection->put('user', $this->resource->user);
            });
    }
}
