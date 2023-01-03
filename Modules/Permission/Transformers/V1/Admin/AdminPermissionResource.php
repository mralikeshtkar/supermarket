<?php

namespace Modules\Permission\Transformers\V1\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class AdminPermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return collect($this->resource)->when($this->whenAppended('name'), function (Collection $collection) {
            $collection->put('translated_name',$this->resource->getTranslatedName());
        });
    }
}
