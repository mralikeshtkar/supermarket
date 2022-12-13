<?php

namespace Modules\Discount\Transformers\V1\Api\Admin;

use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class AdminDiscountResource extends JsonResource
{
    /**
     * @param $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\Illuminate\Support\Collection|\JsonSerializable
     */
    public function toArray($request)
    {
        return collect($this->resource)->when($this->resource->start_at, function (Collection $collection) {
            $collection->put('start_at', Verta::parse($this->resource->start_at)->format('Y/n/j H:i'));
        })->when($this->resource->expire_at, function (Collection $collection) {
            $collection->put('expire_at', Verta::parse($this->resource->expire_at)->format('Y/n/j H:i'));
        })->when($this->resource->originalIsEquivalent('status'), function (Collection $collection) {
            $collection->put('status', $this->resource->status)
                ->put('translated_status', $this->resource->getTranslatedStatus())
                ->put('status_css_class', $this->resource->getStatusClassName());
        });
    }
}
