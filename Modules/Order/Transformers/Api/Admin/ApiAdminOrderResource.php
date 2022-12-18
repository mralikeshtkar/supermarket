<?php

namespace Modules\Order\Transformers\Api\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use function collect;

class ApiAdminOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return collect($this->resource)->when($this->resource->originalIsEquivalent('created_at'), function (Collection $collection) {
            $collection->put('created_at', jalaliFormat($this->resource->created_at));
        })->when($this->resource->originalIsEquivalent('products_count'), function (Collection $collection) {
            $collection->put('products_count', $this->resource->products_count);
        })->when($this->resource->relationLoaded('address'), function (Collection $collection) {
            $collection->put('address', collect($this->resource->address)->when($this->resource->address->originalIsEquivalent('province_name'), function (Collection $collection) {
                $collection->put('province_name', $this->resource->address->province_name);
            })->when($this->resource->address->originalIsEquivalent('city_name'), function (Collection $collection) {
                $collection->put('city_name', $this->resource->address->city_name);
            })->toArray());
        })->when($this->resource->relationLoaded('products'), function (Collection $collection) {
            $collection->put('products', ApiAdminOrderProductsResource::collection($this->resource->products));
        })->toArray();
    }
}
