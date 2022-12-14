<?php

namespace Modules\Order\Entities;

use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Discount\Entities\Discount;
use Modules\Discount\Exceptions\DiscountIsInvalidException;
use Modules\Order\Transformers\Api\Admin\ApiAdminOrderResource;
use Modules\Product\Entities\Product;
use Modules\Setting\Entities\Setting;
use Modules\User\Entities\User;
use Shetabit\Multipay\Invoice as InvoicePayment;
use Shetabit\Payment\Facade\Payment;
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
        'total',
        'discount_amount',
        'amount',
        'shipping_cost',
        'total_cart',
        'discount',
        'status',
    ];

    protected $casts = [
        'status' => 'int',
    ];

    private array $selected_columns = ['*'];

    private array $with_relationships = [];

    private array $with_scopes = [];

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
     * @param $order
     * @return Model|Collection|Builder|array|null
     */
    public function findOrFailById($order): Model|Collection|Builder|array|null
    {
        return self::query()
            ->select($this->selected_columns)
            ->with($this->with_relationships)
            ->scopes($this->with_scopes)
            ->findOrFail($order);
    }

    /**
     * Store an order.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws DiscountIsInvalidException
     */
    public function store(Request $request): JsonResponse
    {
        if ($request->filled('discount'))
            $discount = Discount::init()->withRelationships(['products:id', 'categories:id'])
                ->selectColumns(['code', 'description', 'is_percent', 'amount', 'start_at', 'expire_at', 'created_at'])
                ->findValidDiscountByCode($request->discount);
        else
            $discount = null;
        $cart = collect($request->user()->getCart($discount)->toArray($request));
        $address = $request->user()->findOrFailAddressById($request->address_id);
        if (Cache::get(Setting::SETTING_CACHE_KEY, collect())->get(Setting::SETTING_INACTIVATE_BUY_BUTTON, false)) return ApiResponse::sendError(trans("Shopping is disabled"), Response::HTTP_BAD_REQUEST);
        if ($this->_checkTotalPriceIsNotGreaterThanMinimum($cart)) return ApiResponse::sendError(trans("order::messages.setting_minimum_cart_price", ["price" => number_format($this->_getMinimumCartPrice())]), Response::HTTP_BAD_REQUEST);
//        $request->user()->clearCart();
        $order = self::query()->create([
            'user_id' => $request->user()->id,
            'amount' => $cart->get('total_price'),
            'total' => $cart->get('total'),
            'discount_amount' => $cart->get('discount_amount'),
            'shipping_cost' => $cart->get('shipping_cost'),
            'total_cart' => $cart->get('total_cart'),
            'discount' => $discount,
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
        $payment = Payment::purchase(
            (new InvoicePayment)->amount($cart->get('total_price')),
            function ($driver, $transactionId) use ($order, $cart, $request) {
                $order->invoices()->create([
                    'user_id' => $request->user()->id,
                    'transactionId' => $transactionId,
                    'gateway' => class_basename($driver),
                    'amount' => $cart->get('total_price'),
                ]);
            }
        )->pay();
        return ApiResponse::message(trans('Registration information completed successfully'))
            ->addData('order', ApiAdminOrderResource::make($order))
            ->addData('payment', $payment)
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
            })
            ->when($request->filled('user_name'), function (Builder $builder) use ($request) {
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
            })->paginate();
        return ApiPaginationResource::make($orders)->additional(['itemsResource' => ApiAdminOrderResource::class]);
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function selectColumns(array $columns): static
    {
        $this->selected_columns = $columns;
        return $this;
    }

    /**
     * @param array $relations
     * @return $this
     */
    public function withRelationships(array $relations): static
    {
        $this->with_relationships = $relations;
        return $this;
    }

    /**
     * @param array $scopes
     * @return $this
     */
    public function withScopes(array $scopes): static
    {
        $this->with_scopes = $scopes;
        return $this;
    }

    /**
     * @param $status
     * @return bool
     */
    public function changeStatus($status): bool
    {
        return $this->update(['status'=>$status]);
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
        return $this->hasOne(OrderAddress::class)
            ->select(['id', 'order_id', 'city_id', 'name', 'mobile', 'address', 'postal_code']);
    }

    /**
     * @return HasMany
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    #endregion

}
