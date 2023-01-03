<?php

namespace Modules\Core\Transformers\V1\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class AdminSidebarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return collect(config('sidebar'))->filter(function ($item) {
            return !array_key_exists('permissions', $item) || $this->resource->canAny($item['permissions']);
        })->map(function ($item) {
            return collect($item)->when(array_key_exists('submenu', $item), function (Collection $collection) use ($item) {
                $collection->put('submenu', collect($item['submenu'])->filter(function ($item) {
                    return !array_key_exists('permissions', $item) || $this->resource->canAny($item['permissions']);
                })->toArray());
            })->toArray();
        });
    }
}
