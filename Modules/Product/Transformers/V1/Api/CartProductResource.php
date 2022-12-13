<?php

namespace Modules\Product\Transformers\V1\Api;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Setting\Entities\Setting;

class CartProductResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $products = $this->resource->map(function ($item) {
            $quantity = collect(collect($this->additional['cart'])->get($item->id))->get('quantity');
            return collect($item->toArray())
                ->put('quantity', $quantity)
                ->put('sum_price', $item->price * $quantity)
                ->when($item->relationLoaded('image'), function (Collection $collection) use ($item) {
                    $collection->put('image', $item->image);
                });
        });
        return [
            'products' => $products,
            'total_price' => $products && $products->count() ? $products->sum('sum_price') : 0,
            'inactive_buy_button' => Cache::get(Setting::SETTING_CACHE_KEY, collect())->get(Setting::SETTING_INACTIVATE_BUY_BUTTON, false),
        ];
    }

}
