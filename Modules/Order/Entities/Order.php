<?php

namespace Modules\Order\Entities;

use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Transformers\Api\Admin\ApiOrderResource;
use Modules\Product\Entities\Product;
use Modules\Product\Transformers\V1\Api\CartProductResource;
use Modules\Setting\Entities\Setting;
use Modules\User\Entities\User;
use Modules\User\Transformers\Api\Admin\ApiUserOrdersResource;
use Symfony\Component\HttpFoundation\Response;

class Order extends Model
{
    use HasFactory;

    #region Constance

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'amount',
        'status',
    ];

    #endregion

    #region Methods

    /**
     * @return Order
     */
    public static function init(): Order
    {
        return new self();
    }

    /**
     * Get translated status.
     *
     * @return string
     */
    public function getTranslatedStatus(): string
    {
        return OrderStatus::getDescription(intval($this->status));
    }

    /**
     * @param $order
     * @param array $relations
     * @return Model|Collection|Builder|array|null
     */
    public function findOrFailById($order, array $relations = []): Model|Collection|Builder|array|null
    {
        return self::query()
            ->with($relations)
            ->findOrFail($order);
    }

    /**
     * Get css class status.
     *
     * @return string
     */
    public function getCssClassStatus(): string
    {
        return OrderStatus::coerce(intval($this->status))->getCssClass();
    }

    /**
     * Store an order.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $cart = collect($request->user()->getCart()->toArray($request));
        $address = $request->user()->findOrFailAddressById($request->address_id);
        if (Cache::get(Setting::SETTING_CACHE_KEY, collect())->get(Setting::SETTING_INACTIVATE_BUY_BUTTON, false)) return ApiResponse::sendError(trans("Shopping is disabled"), Response::HTTP_BAD_REQUEST);
        if ($this->_checkTotalPriceIsNotGreaterThanMinimum($cart)) return ApiResponse::sendError(trans("order::messages.setting_minimum_cart_price", ["price" => number_format($this->_getMinimumCartPrice())]), Response::HTTP_BAD_REQUEST);
        $request->user()->clearCart();
        $order = self::query()->create([
            'user_id' => $request->user()->id,
            'amount' => $cart->get('total_price'),
        ]);
        $order->address()->create([
            'city_id' => $address->city_id,
            'name' => $address->name,
            'mobile' => $address->mobile,
            'address' => $address->address,
            'postal_code' => $address->postal_code,
        ]);
        $order->products()->attach($cart->get('products')->mapWithKeys(function ($item) {
            return [$item['id'] => [
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ]];
        }));
        return ApiResponse::message(trans('Registration information completed successfully'))
            ->addData('order', ApiOrderResource::make($order))
            ->send();
    }

    /**
     * @param $cart
     * @return bool
     */
    private function _checkTotalPriceIsNotGreaterThanMinimum($cart): bool
    {
        $total_price = collect($cart)->get('total_price', 0);
        return $total_price < $this->_getMinimumCartPrice();
    }

    /**
     * @return mixed
     */
    private function _getMinimumCartPrice(): mixed
    {
        return Cache::setting()->get(Setting::SETTING_MINIMUM_CART_PRICE, 0);
    }

    /**
     * @param $status
     * @return Order
     */
    public function changeStatus($status): Order
    {
        $this->update(['status' => $status]);
        return $this->refresh();
    }

    /**
     * @param Request $request
     * @return ApiPaginationResource
     */
    public function getAdminIndexPaginate(Request $request): ApiPaginationResource
    {
        $orders = self::query()
            ->withCount('products')
            ->with(['address'])
            ->when($request->filled('order'), function (Builder $builder) use ($request) {
                $builder->where('id', 'LIKE', '%' . $request->order . '%');
            })->when($request->filled('user_name'), function (Builder $builder) use ($request) {
                $builder->whereHas('user', function (Builder $builder) use ($request) {
                    $builder->where('name', 'LIKE', '%' . $request->user_name . '%');
                });
            })->when($request->filled('user_id'), function (Builder $builder) use ($request) {
                $builder->whereHas('user', function (Builder $builder) use ($request) {
                    $builder->where('id', $request->user_name);
                });
            })->when($request->filled('from') && validateDate($request->from), function (Builder $builder) use ($request) {
                $builder->whereDate('created_at', '>=', Verta::parseFormat('Y/m/d', $request->from)->datetime());
            })->when($request->filled('to') && validateDate($request->to), function (Builder $builder) use ($request) {
                $builder->whereDate('created_at', '<=', Verta::parseFormat('Y/m/d', $request->to)->datetime());
            })->when($request->filled('status') && OrderStatus::hasKey(ucfirst($request->status)), function (Builder $builder) use ($request) {
                $builder->where('status', OrderStatus::getValue(ucfirst($request->status)));
            })->paginate();
        return ApiPaginationResource::make($orders)->additional(['itemsResource' => ApiOrderResource::class]);
    }

    #endregion

    #region Relations

    /**
     * @return MorphToMany
     */
    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'orderable', 'orderables')
            ->withPivot(['quantity', 'unit_price']);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasOne
     */
    public function address(): HasOne
    {
        return $this->hasOne(OrderAddress::class);
    }

    #endregion

    #region Scopes

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeSuccess(Builder $builder)
    {
        $builder->where('status', OrderStatus::Success);
    }

    #endregion

}
