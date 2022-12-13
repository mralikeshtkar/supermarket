<?php

namespace Modules\Comment\Transformers\V1\Api\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class AdminCommentResource extends JsonResource
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
            })->when($this->resource->originalIsEquivalent('status'), function (Collection $collection) {
                $collection->put('status', $this->resource->status)
                    ->put('translated_status', $this->resource->getTranslatedStatus())
                    ->put('status_css_class', $this->resource->getStatusClassName());
            })->when($this->resource->relationLoaded('user'), function (Collection $collection) {
                $collection->put('user', $this->resource->user);
            })->when($this->resource->relationLoaded('commentable'), function (Collection $collection) {
                $collection->put('commentable', $this->resource->commentable);
            });
    }
}
