<?php

namespace Modules\News\Transformers\Api\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Modules\News\Enums\NewsCommentStatus;

class ApiAdminCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return collect($this->resource)->when(array_key_exists('created_at', $this->resource->getAttributes()), function (Collection $collection) {
            $collection->put('created_at', verta($this->resource->created_at)->formatJalaliDate());
        })->when(array_key_exists('status', $this->resource->getAttributes()), function (Collection $collection) {
            $collection->put('status_translated', NewsCommentStatus::getDescription($this->resource->status));
        });
    }
}
