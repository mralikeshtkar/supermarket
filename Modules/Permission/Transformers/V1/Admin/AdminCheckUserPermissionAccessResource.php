<?php

namespace Modules\Permission\Transformers\V1\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Permission\Enums\Permissions;

class AdminCheckUserPermissionAccessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return collect(Permissions::asArray())->mapWithKeys(function ($item, $key) {
            return [
                $key => $this->resource->can($item),
            ];
        });
    }
}
