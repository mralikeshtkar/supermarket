<?php

namespace Modules\User\Entities;

use Hekmatinasser\Verta\Verta;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use LaravelIdea\Helper\Modules\User\Entities\_IH_User_C;
use LaravelIdea\Helper\Modules\User\Entities\_IH_User_QB;
use Modules\Address\Entities\Address;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Order\Entities\Order;
use Modules\Product\Entities\Product;
use Modules\Product\Transformers\V1\Api\CartProductResource;
use Modules\Setting\Entities\Setting;
use Modules\User\Database\factories\UserFactory;
use Modules\User\Exceptions\NotEnoughProductInCartException;
use Modules\User\Exceptions\NotEnoughProductStockException;
use Modules\User\Transformers\V1\Api\ApiUserOrdersResource;
use Shetabit\Visitor\Traits\Visitor;
use Spatie\Permission\Traits\HasRoles;
use Symfony\Component\HttpFoundation\Response;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Visitor;

    #region Constants

    protected $fillable = [
        'mobile',
        'email',
        'name',
        'password',
        'cart',
        'last_seen_products',
        'is_blocked',
    ];

    protected $guard_name = 'sanctum';

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'cart' => 'array',
        'last_seen_products' => 'array',
        'is_blocked' => 'bool',
    ];

    private array $selected_columns = ['*'];

    private array $with_relationships = [];

    private array $with_scopes = [];

    #endregion

    #region Mutators

    /**
     * @return string
     */
    public function getCreatedAtFaAttribute(): string
    {
        return Verta::instance($this->created_at)->formatJalaliDate();
    }

    #endregion

    #region Methods

    /**
     * Init models factory;
     *
     * @return UserFactory
     */
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    /**
     * Initialize class.
     *
     * @return User
     */
    public static function init(): User
    {
        return new self();
    }

    /**
     * @param $product
     * @return void
     */
    public function addLastSeenProduct($product)
    {
        $collection = collect(Arr::get($this, 'last_seen_products', []))->reject(function ($item) use ($product) {
            return $item == $product;
        });
        //todo set from setting
        if ($collection->count() > 50) $collection->shift();
        $this->update(['last_seen_products' => $collection->push($product)]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getUser(Request $request): mixed
    {
        return $request->user()->load('roles');
    }

    /**
     * @param $user
     * @return mixed
     */
    public function findOrFailById($user): mixed
    {
        return self::query()->select($this->selected_columns)->with($this->with_relationships)
            ->scopes($this->with_scopes)->findOrFail($user);
    }

    /**
     * @return bool|null
     */
    public function deleteUser(): ?bool
    {
        return $this->delete();
    }

    /**
     * @param Request $request
     * @return User
     */
    public function updateUser(Request $request): User
    {
        $this->update([
            'name' => $request->name,
            'mobile' => to_valid_mobile_number($request->mobile),
            'email' => $request->email,
            'is_blocked' => $request->is_blocked,
        ]);
        if ($request->filled('role')) $this->assignRole($request->role);
        return $this->refresh();
    }

    /**
     * @param $data
     * @return bool
     */
    public function updateInformation($data): bool
    {
        return $this->update($data);
    }

    /**
     * @param $old_password
     * @return bool
     */
    public function checkPassword($old_password): bool
    {
        return Hash::check($old_password, $this->password);
    }

    /**
     * @param Request $request
     * @return Model|Builder|User|_IH_User_QB
     */
    public function store(Request $request): Model|Builder|User|_IH_User_QB
    {
        return self::query()->create([
            'name' => $request->name,
            'mobile' => to_valid_mobile_number($request->mobile),
            'email' => $request->email,
        ]);
    }

    /**
     * @param mixed $mobile
     * @param mixed $except
     * @return bool
     */
    public function mobileIsUnique(mixed $mobile, mixed $except): bool
    {
        return !self::query()
            ->where('mobile', $mobile)
            ->when($except, function (Builder $builder) use ($except) {
                $builder->whereNot('id', $except);
            })->exists();
    }

    /**
     * Add favouritable to user favourite.
     *
     * @param Request $request
     * @param string $column
     * @return mixed
     */
    public function like(Request $request, string $column = 'id'): mixed
    {
        $favouritable = $request->favouritable['type']::where($column, $request->favouritable['id'])->firstOrFail();
        $favouritable->favouriteUsers()->syncWithoutDetaching($this->id);
        return $favouritable;
    }

    /**
     * Remove favouritable from user favourite.
     *
     * @param Request $request
     * @param string $column
     * @return mixed
     */
    public function dislike(Request $request, string $column = 'id'): mixed
    {
        $favouritable = $request->favouritable['type']::where($column, $request->favouritable['id'])->firstOrFail();
        $favouritable->favouriteUsers()->detach($this->id);
        return $favouritable;
    }

    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function storeCart(Request $request, $product): JsonResponse
    {
        $cart = collect(auth()->user()->cart);
        $quantity = Arr::get($cart->get($product->id, []), 'quantity', 0);
        if ($this->_TotalPriceIsGreaterThanMaximum($product, $quantity)) return ApiResponse::sendError(trans('user::messages.setting_maximum_cart_price', ['price' => number_format($this->_getMaximumCartPrice())]), Response::HTTP_BAD_REQUEST);
        if ($product->stock < $request->quantity + $quantity) return ApiResponse::sendError(trans('user::messages.not_enough_product_stock_exception'), Response::HTTP_BAD_REQUEST);
        $this->update([
            'cart' => $cart->put($product->id, [
                'quantity' => $quantity + $request->quantity
            ]),
        ]);
        return ApiResponse::message(trans('user::messages.product_added_to_cart'))
            ->addData('cart', $this->getCart())
            ->send();
    }

    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function updateCart(Request $request, $product): JsonResponse
    {
        $cart = collect(auth()->user()->cart);
        $quantity = Arr::get($cart->get($product->id, []), 'quantity', 0);
        if ($quantity < $request->quantity) return ApiResponse::sendError(trans('user::messages.not_enough_product_in_cart'), Response::HTTP_BAD_REQUEST);
        $this->update([
            'cart' => $cart->when($quantity - $request->quantity, function (Collection $collection) use ($product, $quantity, $request) {
                $collection->put($product->id, [
                    'quantity' => $quantity - $request->quantity
                ]);
            }, function (Collection $collection) use ($product) {
                $collection->forget($product->id);
            }),
        ]);
        return ApiResponse::message(trans('user::messages.product_quantity_cart_has_been_changed'))
            ->addData('cart', $this->getCart())
            ->send();
    }

    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function updateCartProduct(Request $request, $product): JsonResponse
    {
        $cart = collect(auth()->user()->cart);
        if ($this->_TotalPriceIsGreaterThanMaximum($product, $request->quantity)) return ApiResponse::sendError(trans('user::messages.setting_maximum_cart_price', ['price' => number_format($this->_getMaximumCartPrice())]), Response::HTTP_BAD_REQUEST);
        if ($product->stock < $request->quantity) return ApiResponse::sendError(trans('user::messages.not_enough_product_stock_exception'), Response::HTTP_BAD_REQUEST);
        $this->update([
            'cart' => $cart->put($product->id, [
                'quantity' => $request->quantity
            ]),
        ]);
        return ApiResponse::message(trans('user::messages.product_quantity_cart_has_been_changed'))
            ->addData('cart', $this->getCart())
            ->send();
    }

    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function destroyCartProduct(Request $request, $product): JsonResponse
    {
        $cart = collect(auth()->user()->cart);
        $this->update(['cart' => $cart->forget($product->id),]);
        return ApiResponse::message(trans('Registration information completed successfully'))
            ->addData('cart', $this->getCart())
            ->send();
    }

    /**
     * @return mixed
     */
    public function getOnlineUsers(): mixed
    {
        return User::online()
            ->select(['id', 'mobile', 'email', 'name'])
            ->paginate();
    }

    /**
     * @return CartProductResource
     */
    public function getCart(): CartProductResource
    {
        return Product::init()->getCartData($this);
    }

    /**
     * @return bool
     */
    public function clearCart(): bool
    {
        return $this->update(['cart' => null]);
    }

    public function getAdminIndexPaginate(Request $request): LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|_IH_User_C|array
    {
        return self::query()
            ->with('roles')
            ->when($request->filled('name'), function (Builder $builder) use ($request) {
                $builder->where('name', 'LIKE', "%" . $request->name . "%");
            })->when($request->filled('role'), function (Builder $builder) use ($request) {
                $builder->whereHas('roles', function (Builder $builder) use ($request) {
                    $builder->where('name', $request->role);
                });
            })->when($request->filled('date') && validateDate($request->date), function (Builder $builder) use ($request) {
                $builder->whereDate('created_at', Verta::createFromFormat('Y/m/d', Verta::parseFormat('Y/m/d', $request->date)->datetime()));
            })->when($request->filled('email'), function (Builder $builder) use ($request) {
                $builder->where('email', 'LIKE', "%" . $request->email . "%");
            })->when($request->filled('mobile'), function (Builder $builder) use ($request) {
                $builder->where('mobile', 'LIKE', "%" . ltrim($request->mobile, '0') . "%");
            })->latest()
            ->paginate()
            ->appends($request->only(['name', 'date', 'email', 'mobile', 'role']));
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|array|\LaravelIdea\Helper\Modules\Address\Entities\_IH_Address_C
     */
    public function getFavourites(Request $request): LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|array|\LaravelIdea\Helper\Modules\Address\Entities\_IH_Address_C
    {
        return $this->favourites()
            ->with(['image', 'model'])
            ->latest()
            ->paginate();
    }

    /**
     * Get orders as pagination.
     *
     * @return ApiPaginationResource
     */
    public function getOrders(): ApiPaginationResource
    {
        return ApiPaginationResource::make($this->orders()
            ->with(['user:id,mobile,name', 'address' => function (HasOne $hasOne) {
                $hasOne->withAggregate('city', 'name')
                    ->withAggregate('province AS province_name', 'provinces.name');
            }])
            ->paginate())->additional(['itemsResource' => ApiUserOrdersResource::class]);
    }

    /**
     * @param $order
     * @return Model|\Illuminate\Database\Eloquent\Collection|HasMany|array|null
     */
    public function findOrFailOrderById($order): Model|\Illuminate\Database\Eloquent\Collection|HasMany|array|null
    {
        return $this->orders()
            ->with(['user:id,mobile,name', 'products', 'products.image', 'address' => function (HasOne $hasOne) {
                $hasOne->withAggregate('city', 'name')
                    ->withAggregate('province AS province_name', 'provinces.name');
            }])->findOrFail($order);
    }

    public function findOrFailAddressById($address)
    {
        return $this->addresses()->findOrFail($address);
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
     * @param $product
     * @param mixed $quantity
     * @return bool
     */
    private function _TotalPriceIsGreaterThanMaximum($product, mixed $quantity): bool
    {
        $total_price = collect($this->getCart())->get('total_price', 0) + ($product->price * $quantity);
        $maximum = $this->_getMaximumCartPrice();
        return !is_null($maximum) && $total_price > $maximum;
    }

    /**
     * @return mixed
     */
    private function _getMaximumCartPrice(): mixed
    {
        return Cache::setting()->get(Setting::SETTING_MAXIMUM_CART_PRICE, null);
    }

    #endregion

    #region Relationships

    /**
     * User addresses.
     *
     * @return HasMany
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'user_id');
    }

    /**
     * User favorites.
     *
     * @return MorphToMany
     */
    public function favourites(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'favouritable', 'favourites');
    }

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    #endregion

}
