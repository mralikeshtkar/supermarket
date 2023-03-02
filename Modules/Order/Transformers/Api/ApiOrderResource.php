<?php

namespace Modules\Order\Transformers\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Modules\Product\Transformers\V1\Api\ProductResource;
use Modules\Transportation\Transformers\Api\ApiOrderInvoiceResource;
use function collect;

class ApiOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return collect($this->resource)
            ->when($this->resource->originalIsEquivalent('products_count'), function (Collection $collection) {
                $collection->put('products_count', $this->resource->products_count);
            })->when(array_key_exists('amount', $this->resource->toArray()), function (Collection $collection) {
                $collection->put('formatted_amount', number_format($this->resource->amount));
            })->when(array_key_exists('created_at', $this->resource->toArray()), function (Collection $collection) {
                $collection->put('created_at', jalaliFormat($this->resource->created_at));
            })->when($this->resource->relationLoaded('address'), function (Collection $collection) {
                $collection->put('address', collect($this->resource->address)->when($this->resource->address->originalIsEquivalent('province_name'), function (Collection $collection) {
                    $collection->put('province_name', $this->resource->address->province_name);
                })->when($this->resource->address->originalIsEquivalent('city_name'), function (Collection $collection) {
                    $collection->put('city_name', $this->resource->address->city_name);
                })->toArray());
            })->when($this->resource->relationLoaded('products'), function (Collection $collection) {
                $collection->put('products', ProductResource::collection($this->resource->products));
            })->when($this->resource->relationLoaded('invoices'), function (Collection $collection) {
                $collection->put('invoices', ApiOrderInvoiceResource::collection($this->resource->invoices));
            })->toArray();
    }
}
