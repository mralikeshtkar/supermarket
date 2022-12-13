<?php

namespace Modules\User\Transformers\V1\Api\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class AdminUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return collect($this->resource)->when($this->resource->originalIsEquivalent('created_at'), function (Collection $collection) {
            $collection->put('created_at', verta($this->resource->created_at)->formatJalaliDate());
        })->when($this->resource->relationLoaded('roles'),function (Collection $collection){
            $collection->forget('roles')->put('role',$this->resource->roles->first());
        });
    }
}
