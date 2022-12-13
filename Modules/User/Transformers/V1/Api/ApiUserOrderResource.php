<?php

namespace Modules\User\Transformers\V1\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use function collect;

class ApiUserOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return collect([
            'id' => $this->resource->id,
            'user_id' => $this->resource->user_id,
            'status_id' => $this->resource->status,
            'address_id' => $this->resource->address_id,
            'amount' => $this->resource->amount,
            'formatted_amount' => number_format($this->resource->amount),
            'translated_status' => $this->resource->getTranslatedStatus(),
            'created_at' => jalaliFormat($this->resource->created_at),
        ])->when($this->resource->relationLoaded('user'), function (Collection $collection) {
            $collection->put('user', $this->resource->user);
        })->when($this->resource->relationLoaded('address'), function (Collection $collection) {
            $collection->put('address', collect([
                'id' => $this->resource->address->id,
                'province_id' => $this->resource->address->province_id,
                'city_id' => $this->resource->address->city_id,
                'name' => $this->resource->address->name,
                'mobile' => $this->resource->address->mobile,
                'address' => $this->resource->address->address,
                'postal_code' => $this->resource->address->postal_code,
            ])->when($this->resource->address->originalIsEquivalent('province_name'), function (Collection $collection) {
                $collection->put('province_name', $this->resource->address->province_name);
            })->when($this->resource->address->originalIsEquivalent('city_name'), function (Collection $collection) {
                $collection->put('city_name', $this->resource->address->city_name);
            })->toArray());
        })->when($this->resource->relationLoaded('products'),function (Collection $collection){
            $collection->put('products',ApiUserOrderProductsResource::collection($this->resource->products));
        })->toArray();
    }
}
