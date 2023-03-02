<?php

namespace Modules\Transportation\Transformers\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Modules\Order\Enums\OrderInvoiceStatus;

class ApiOrderInvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return collect(parent::toArray($request))->when(array_key_exists('status', $this->resource->toArray()), function (Collection $collection) {
            $collection->put('translated_status', OrderInvoiceStatus::getDescription($this->resource->status));
        })->when(array_key_exists('created_at', $this->resource->toArray()), function (Collection $collection) {
            $collection->put('created_at', jalaliFormat($this->resource->created_at,'Y/m/d H:i:s'));
        });
    }
}
