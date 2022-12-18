<?php

namespace Modules\Product\Transformers\V1\Api;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Discount\Exceptions\DiscountCodeCannotUseForThisShoppingCartException;
use Modules\Setting\Entities\Setting;
use Symfony\Component\HttpFoundation\Response;

class CartProductResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     * @throws DiscountCodeCannotUseForThisShoppingCartException
     */
    public function toArray($request)
    {
        $shipping_cost = Cache::get(Setting::SETTING_CACHE_KEY, collect())->get(Setting::SETTING_SHIPPING_COST, 0);
        $products = $this->resource->map(function ($item) {
            $quantity = collect(collect($this->additional['cart'])->get($item->id))->get('quantity');
            return collect($item->toArray())
                ->put('quantity', $quantity)
                ->put('unit_price', $item->final_price)
                ->put('sum_price', $item->final_price * $quantity)
                ->when($item->relationLoaded('image'), function (Collection $collection) use ($item) {
                    $collection->put('image', $item->image);
                });
        });
        $total_cart = $products && $products->count() ? $products->sum('sum_price') : 0;
        $total_price = $total_cart + $shipping_cost;
        $result = collect([
            'products' => $products,
            'shipping_cost' => $shipping_cost,
            'total_cart' => $total_cart,
            'total' => $total_price,
            'total_price' => $total_price,
            'discount_amount' => 0,
            'inactive_buy_button' => Cache::get(Setting::SETTING_CACHE_KEY, collect())->get(Setting::SETTING_INACTIVATE_BUY_BUTTON, false),
        ]);
        if ($this->_discount() && $total_price) {
            if ($this->_discountProducts()->count() || $this->_discountCategories()->count()) {
                if ($this->_discountProducts()->count()) {
                    $ids = $this->_discountProducts()->intersect($products->pluck('id'));
                    if (!$ids->count()) throw new DiscountCodeCannotUseForThisShoppingCartException(trans("The discount code cannot be used for this shopping cart"), Response::HTTP_BAD_REQUEST);
                    $selected_products_price = $products->whereIn('id', $ids)->sum('sum_price');
                    $unselected_products_price = $products->whereNotIn('id', $ids)->sum('sum_price');
                } else {
                    $ids = $this->_discountCategories()->intersect($products->pluck('categories')->flatten(1)->pluck('id'));
                    if (!$ids->count()) throw new DiscountCodeCannotUseForThisShoppingCartException(trans("The discount code cannot be used for this shopping cart"), Response::HTTP_BAD_REQUEST);
                    $selected_products_price = $products->filter(fn($item) => collect($item['categories'])->whereIn('id', $ids)->count())->sum('sum_price');
                    $unselected_products_price = $products->filter(fn($item) => collect($item['categories'])->whereNotIn('id', $ids)->count())->sum('sum_price');
                }
                $discount_amount = $this->_calculateDiscountAmount($selected_products_price);
                $result->put('discount_amount', $discount_amount)
                    ->put('total_price', $this->_calculateTotalPrice($selected_products_price, $discount_amount) + $unselected_products_price);
            } else {
                $discount_amount = $this->_calculateDiscountAmount($total_price);
                $result->put('discount_amount', $discount_amount)
                    ->put('total_price', $this->_calculateTotalPrice($total_price, $discount_amount));
            }
        }
        return $result;
    }

    /**
     * @return mixed
     */
    private function _discount(): mixed
    {
        return $this->additional['discount'];
    }

    private function _discountProducts()
    {
        return $this->_discount()->products->pluck('id');
    }

    private function _discountCategories()
    {
        return $this->_discount()->categories->pluck('id');
    }

    private function _discountIsPercent()
    {
        return $this->_discount()->is_percent;
    }

    private function _discountAmount()
    {
        return $this->_discount()->amount;
    }

    /**
     * @param $total_price
     * @return float|int
     */
    private function _calculateDiscountAmount($total_price): float|int
    {
        return $this->_discountIsPercent() ?
            ($total_price / 100) * $this->_discountAmount() :
            $this->_discountAmount();
    }

    /**
     * @param $total_price
     * @param $discount
     * @return int
     */
    private function _calculateTotalPrice($total_price, $discount): int
    {
        return $total_price < $discount ? 0 : $total_price - $discount;
    }

}
