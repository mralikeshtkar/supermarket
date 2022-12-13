<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    #region Constance

    const SETTING_SHOP_IS_OPEN = "shop_is_open";
    const SETTING_SPECIAL_PRODUCTS_LIMIT = "special_products_limit";
    const SETTING_MINIMUM_CART_PRICE = "setting_minimum_cart_price";
    const SETTING_MAXIMUM_CART_PRICE = "setting_maximum_cart_price";
    const SETTING_INACTIVATE_BUY_BUTTON = "inactivate_buy_button";
    const SETTING_SHIPPING_COST = "shipping_cost";

    const SETTING_KEYS = [
        self::SETTING_SHOP_IS_OPEN,
        self::SETTING_SPECIAL_PRODUCTS_LIMIT,
        self::SETTING_MINIMUM_CART_PRICE,
        self::SETTING_MAXIMUM_CART_PRICE,
        self::SETTING_INACTIVATE_BUY_BUTTON,
        self::SETTING_SHIPPING_COST,
    ];

    const SETTING_RULES = [
        self::SETTING_SHOP_IS_OPEN => ['nullable', 'boolean'],
        self::SETTING_SPECIAL_PRODUCTS_LIMIT => ['nullable', 'numeric', 'min:1'],
        self::SETTING_MINIMUM_CART_PRICE => ['nullable', 'numeric', 'min:1'],
        self::SETTING_MAXIMUM_CART_PRICE => ['nullable', 'numeric', 'min:1'],
        self::SETTING_INACTIVATE_BUY_BUTTON => ['nullable', 'boolean'],
        self::SETTING_SHIPPING_COST => ['nullable', 'numeric', 'min:0'],
    ];

    const SETTING_BOOLEAN = [
        self::SETTING_SHOP_IS_OPEN,
        self::SETTING_INACTIVATE_BUY_BUTTON,
    ];

    const SETTING_CACHE_KEY = "settings";

    protected $fillable = [
        'key',
        'value',
    ];

    #endregion

    #region Public methods

    /**
     * @return Setting
     */
    public static function init(): Setting
    {
        return new self();
    }

    /**
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        Cache::forget(self::SETTING_CACHE_KEY);
        foreach (self::SETTING_KEYS as $key) {
            self::query()->updateOrCreate([
                'key' => $key,
            ], [
                'key' => $key,
                'value' => $request->get($key),
            ]);
        }
        Cache::rememberForever(Setting::SETTING_CACHE_KEY, function () {
            return self::getSettings()->pluck('value', 'key')->filter();
        });
    }

    public function getSettings(): Collection
    {
        return self::query()->select(['id', 'key', 'value'])->get();
    }

    #endregion


}
