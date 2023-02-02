<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Media\Entities\Media;
use Modules\Media\Traits\HasMedia;

class Setting extends Model
{
    use HasFactory, HasMedia;

    #region Constance

    const MEDIA_COLLECTION_SETTINGS = "settings";

    const MEDIA_DIRECTORY_SETTINGS = "settings";

    const SETTING_SHOP_IS_OPEN = "shop_is_open";
    const SETTING_SPECIAL_PRODUCTS_LIMIT = "special_products_limit";
    const SETTING_MINIMUM_CART_PRICE = "setting_minimum_cart_price";
    const SETTING_MAXIMUM_CART_PRICE = "setting_maximum_cart_price";
    const SETTING_INACTIVATE_BUY_BUTTON = "inactivate_buy_button";
    const SETTING_SHIPPING_COST = "shipping_cost";
    const SETTING_SAMANDEHI_LOGO = "samandehi_logo";
    const SETTING_ENAMAD_LOGO = "enamad_logo";
    const SETTING_INSTAGRAM_ADDRESS = "instagram_address";
    const SETTING_ONLINE_PAY_DISCOUNT = "online_pay_discount";

    const SETTING_KEYS = [
        self::SETTING_SHOP_IS_OPEN,
        self::SETTING_SPECIAL_PRODUCTS_LIMIT,
        self::SETTING_MINIMUM_CART_PRICE,
        self::SETTING_MAXIMUM_CART_PRICE,
        self::SETTING_INACTIVATE_BUY_BUTTON,
        self::SETTING_SHIPPING_COST,
        self::SETTING_SAMANDEHI_LOGO,
        self::SETTING_ENAMAD_LOGO,
        self::SETTING_INSTAGRAM_ADDRESS,
        self::SETTING_ONLINE_PAY_DISCOUNT,
    ];

    const SETTING_RULES = [
        self::SETTING_SHOP_IS_OPEN => ['nullable', 'boolean'],
        self::SETTING_SPECIAL_PRODUCTS_LIMIT => ['nullable', 'numeric', 'min:1'],
        self::SETTING_MINIMUM_CART_PRICE => ['nullable', 'numeric', 'min:1'],
        self::SETTING_MAXIMUM_CART_PRICE => ['nullable', 'numeric', 'min:1'],
        self::SETTING_INACTIVATE_BUY_BUTTON => ['nullable', 'boolean'],
        self::SETTING_SHIPPING_COST => ['nullable', 'numeric', 'min:0'],
        self::SETTING_SAMANDEHI_LOGO => ['nullable', 'image'],
        self::SETTING_ENAMAD_LOGO => ['nullable', 'image'],
        self::SETTING_INSTAGRAM_ADDRESS => ['nullable', 'url'],
        self::SETTING_ONLINE_PAY_DISCOUNT => ['nullable', 'numeric', 'min:1'],
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
            $setting = self::query()->updateOrCreate([
                'key' => $key,
            ], [
                'key' => $key,
                'value' => $request->hasFile($key) ? null : $request->get($key),
            ]);
            if ($request->hasFile($key)) {
                $setting->removeAll(self::MEDIA_COLLECTION_SETTINGS)
                    ->setCollection(self::MEDIA_COLLECTION_SETTINGS)
                    ->setDirectory(self::MEDIA_DIRECTORY_SETTINGS)
                    ->addMedia($request->file($key));
            }
        }
        Cache::rememberForever(Setting::SETTING_CACHE_KEY, function () {
            return self::getSettings()->filter(function ($item) {
                return !is_null($item->value) || $item->file;
            })->mapWithKeys(function ($item){
                return [
                    $item->key => !is_null($item->value) ? (in_array($item->key, Setting::SETTING_BOOLEAN) ? boolval($item->value) : $item->value) : $item->file ,
                ];
            });
        });
    }

    public function getSettings(): Collection
    {
        return self::query()->with('file')->select(['id', 'key', 'value'])->get();
    }

    #endregion

    #region Relations

    public function file(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')
            ->select('id', 'model_id', 'model_type', 'disk', 'files')
            ->where('collection', self::MEDIA_COLLECTION_SETTINGS);
    }

    #endregion


}
