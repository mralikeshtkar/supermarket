<?php

namespace Modules\Vote\Transformers\Api\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Modules\Vote\Enums\VoteStatus;

class ApiAdminVoteResource extends JsonResource
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
            $collection->put('status', VoteStatus::getDescription($this->resource->status));
        })->when($this->resource->relationLoaded('items'), function (Collection $collection) {
            $collection->put('items', ApiAdminVoteItemResource::collection($this->resource->items,['item_users_count'=>$this->resource->item_users_count]));
        })->when($this->resource->relationLoaded('selectedItem'), function (Collection $collection) {
            $collection->put('selected_item', optional($this->resource->selectedItem)->id);
        });
    }
}
