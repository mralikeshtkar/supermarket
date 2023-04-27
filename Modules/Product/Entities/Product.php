<?php

namespace Modules\Product\Entities;

use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use LaravelIdea\Helper\Modules\Media\Entities\_IH_Media_QB;
use LaravelIdea\Helper\Modules\Product\Entities\_IH_Product_C;
use LaravelIdea\Helper\Modules\Product\Entities\_IH_Product_QB;
use Modules\Brand\Entities\Brand;
use Modules\Category\Entities\Category;
use Modules\Category\Traits\HasCategory;
use Modules\Comment\Traits\HasComment;
use Modules\Core\Traits\EloquentHelper;
use Modules\Discount\Traits\HasDiscount;
use Modules\Feature\Traits\HasAttribute;
use Modules\Media\Entities\Media;
use Modules\Media\Traits\HasMedia;
use Modules\Order\Entities\Order;
use Modules\Order\Enums\OrderStatus;
use Modules\Product\Database\factories\ProductFactory;
use Modules\Product\Enums\ProductStatus;
use Modules\Product\Transformers\V1\Api\CartProductResource;
use Modules\Rack\Entities\RackRow;
use Modules\Storeroom\Entities\StoreroomEntrance;
use Modules\Tag\Traits\HasTag;
use Modules\User\Traits\HasFavouritable;
use Staudenmeir\EloquentHasManyDeep\Eloquent\Relations\Traits\HasEagerLimit;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Product extends Model
{
    use HasRelationships,HasEagerLimit, HasFactory, EloquentHelper, HasCategory, HasTag, HasMedia, HasComment, HasAttribute, HasRelationships, HasDiscount, HasFavouritable;

    #region Constance

    const MORPH_CLASS = "product";

    protected $fillable = [
        'user_id',
        'unit_id',
        'brand_id',
        'name',
        'slug',
        'price',
        'status',
        'old_price',
        'additional_price',
        'quantity',
        'manufacturer_price',
        'delivery_is_free',
        'has_tax_exemption',
        'description',
    ];

    protected $casts = [
        'global_discount' => 'array',
        'price' => 'integer',
        'status' => 'integer',
        'delivery_is_free' => 'bool',
        'has_tax_exemption' => 'bool',
    ];

    private array $selected_columns = ['*'];

    private array $with_relationships = [];

    private array $with_scopes = [];

    #endregion

    #region Methods

    protected static function boot()
    {
        parent::boot();
        static::deleted(function (Product $product) {
            $product->removeAllMedia();
        });
    }

    /**
     * @return string
     */
    public function getTranslatedStatus(): string
    {
        return ProductStatus::getDescription($this->status);
    }

    /**
     * @return string
     */
    public function getStatusClassName(): string
    {
        return ProductStatus::fromValue($this->status)->getCssClass();
    }

    /**
     * Init models factory;
     *
     * @return ProductFactory
     */
    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    /**
     * Initialize class.
     *
     * @return Product
     */
    public static function init(): Product
    {
        return new self();
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function allStocks(Request $request): Collection
    {
        return self::query()
            ->select(['id', 'name'])
            ->when($request->filled('search'), function (Builder $builder) use ($request) {
                $builder->where('name', 'LIKE', '%' . $request->search . '%');
            })->when($request->filled('products') && is_array($request->products), function (Builder $builder) use ($request) {
                $builder->orWhereIn('id', $request->products);
            })->hasStock(1)
            ->get();
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAdminStocks(Request $request): LengthAwarePaginator
    {
        return self::query()
            ->select(['id', 'name','quantity'])
            ->with(['image'])
            ->when($request->filled('name'), function (Builder $builder) use ($request) {
                $builder->where('name', 'LIKE', '%' . $request->name . '%');
            })->when($request->filled('min'), function (Builder $builder) use ($request) {
                $builder->where('name', 'LIKE', '%' . $request->name . '%');
            })->stock()
            ->hasStock($request->min, $request->max)
            ->paginate();
    }

    /**
     * @param Request $request
     * @param $category
     * @return LengthAwarePaginator
     */
    public function search(Request $request, $category = null): LengthAwarePaginator
    {
        return self::query()
            ->select(['id', 'name', 'price', 'unit_id'])
            ->with(['image','rack_rows'=>function($q){
                $q->select(['rack_rows.id','rack_rows.rack_id','rack_rows.status'])->limit(1);
            }])
            ->where(function (Builder $builder) use ($request, $category) {
                $builder->when(!is_null($category), function (Builder $builder) use ($category) {
                    $builder->whereHas('categories', function (Builder $builder) use ($category) {
                        $builder->where('id', $category->id);
                    });
                })->when($request->filled('search'), function (Builder $builder) use ($request) {
                    $builder->where('name', 'LIKE', '%' . $request->search . '%');
                })->when($request->filled('brands'), function (Builder $builder) use ($request) {
                    $builder->whereIn('brand_id', $request->brands);
                })->when($request->filled('filters'), function (Builder $builder) use ($request) {
                    $builder->whereHas('attributes', function (Builder $builder) use ($request) {
                        foreach ($request->filters as $filter => $attributes) {
                            $builder->orWhere(function (Builder $builder) use ($filter, $attributes) {
                                $builder->where('feature_id', $filter)
                                    ->whereIn('option_id', $attributes);
                            });
                        }
                    });
                });
            })->unitName()
            ->accepted()
            ->paginate();
    }

    /**
     * @param $user
     * @return _IH_Product_C|array|LengthAwarePaginator
     */
    public function notPurchased($user): _IH_Product_C|array|LengthAwarePaginator
    {
        return self::query()
            ->select(['id', 'name', 'price'])
            ->with(['image'])
            ->whereDoesntHave('orders', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('status', OrderStatus::DeliveryToCustomer);
            })
            ->accepted()
            ->paginate();
    }

    /**
     * @param $user
     * @return LengthAwarePaginator
     */
    public function latestSeen($user): LengthAwarePaginator
    {
        $last_seen = Arr::get($user, 'last_seen_products', []);
        return self::query()
            ->select(['id', 'name', 'price', 'unit_id'])
            ->with(['image', 'model'])
            ->whereIn('id', $last_seen)
            ->when(count($last_seen), function (Builder $builder) use ($last_seen) {
                $builder->orderByRaw("ARRAY_POSITION(ARRAY[" . implode(",", array_reverse($last_seen)) . "],products.id)");
            })->unitName()
            ->accepted()
            ->paginate();
    }

    /**
     * @param Request $request
     * @return _IH_Product_C|array|LengthAwarePaginator
     */
    public function latestProducts(Request $request): _IH_Product_C|array|LengthAwarePaginator
    {
        return self::query()
            ->select(['id', 'name', 'price'])
            ->with(['image', 'model'])
            ->latest()
            ->accepted()
            ->paginate();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator|_IH_Product_C|Product[]
     */
    public function searchAll(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator|_IH_Product_C|array|LengthAwarePaginator
    {
        return self::query()
            ->with('image')
            ->when($request->filled('search'), function (Builder $builder) use ($request) {
                $builder->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('slug', 'LIKE', '%' . $request->search . '%');
            })
            ->paginate(2)
            ->appends($request->only('search'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|_IH_Product_C|array|LengthAwarePaginator
     */
    public function onlyAccepted(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator|_IH_Product_C|array|LengthAwarePaginator
    {
        return self::query()
            ->with('image')
            ->select(['id', 'name', 'slug', 'price'])
            ->paginate();
    }

    /**
     * @param Request $request
     * @return _IH_Product_C|array|LengthAwarePaginator
     */
    public function getRackRowProducts(Request $request): _IH_Product_C|array|LengthAwarePaginator
    {
        return self::query()
            ->with('image')
            ->when($request->filled('search'), function (Builder $builder) use ($request) {
                $builder->where(function (Builder $builder) use ($request) {
                    $builder->where('name', 'LIKE', '%' . $request->search . '%')
                        ->orWhere('slug', 'LIKE', '%' . $request->search . '%');
                });
            })
            ->accepted()
            ->paginate()
            ->appends($request->only('search'));
    }

    /**
     * @return mixed
     */
    public function getMaximumPrice(): mixed
    {
        return self::query()->max('price');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator|_IH_Product_C|Product[]
     */
    public function getAdminIndexPaginate(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator|_IH_Product_C|array|LengthAwarePaginator
    {
        return self::query()
            ->select(['id', 'name', 'price', 'status', 'created_at'])
            ->with('image')
            ->withAggregate('brand', 'name')
            ->latest()
            ->when($request->filled('name'), function (Builder $builder) use ($request) {
                $builder->where('name', 'LIKE', '%' . $request->name . '%');
            })->when($request->filled('min'), function (Builder $builder) use ($request) {
                $builder->where('price', '>=', $request->min);
            })->when($request->filled('max'), function (Builder $builder) use ($request) {
                $builder->where('price', '<=', $request->max);
            })->paginate(5);
    }

    /**
     * @param $product
     * @return Model|Collection|_IH_Product_C|Product|Builder|array|_IH_Product_QB|null
     */
    public function findOrFailById($product): Model|Collection|_IH_Product_C|Product|Builder|array|_IH_Product_QB|null
    {
        return self::query()->select($this->selected_columns)->with($this->with_relationships)
            ->scopes($this->with_scopes)->findOrFail($product);
    }

    /**
     * @param $product
     * @return Model|Collection|_IH_Product_C|Product|Builder|array|_IH_Product_QB
     */
    public function findOrFailByIdCustomException($product): Model|Collection|_IH_Product_C|Product|Builder|array|_IH_Product_QB
    {
        if ($product = self::query()->find($product))
            return $product;
        throw new ModelNotFoundException(trans('product::messages.product_not_found'), Response::HTTP_NOT_FOUND);
    }

    /**
     * @param $value
     * @return bool
     */
    public function existsStoreroomEntrance($value): bool
    {
        return self::query()
            ->whereHas('storeroom_entrances', function (Builder $builder) use ($value) {
                $builder->where('id', Arr::get($value, 'storeroom_entrance_id'))
                    ->where('product_storeroom_entrance.quantity', '>=', Arr::get($value, 'quantity'));
            })->where('id', Arr::get($value, 'product_id'))
            ->exists();
    }

    /**
     * @param Request $request
     * @return Model|Media
     */
    public function uploadGallery(Request $request): Model|Media
    {
        return $this->setDirectory('products')
            ->setCollection(config('product.collection_gallery'))
            ->addMedia($request->image);
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            /** @var Request $request */
            $product = self::query()->create([
                'user_id' => $request->user()->id,
                'brand_id' => $request->brand_id,
                'unit_id' => $request->unit_id,
                'name' => $request->name,
                'slug' => $request->slug,
                'price' => $request->price,
                'status' => ProductStatus::Pending,
                'old_price' => $request->old_price,
                'additional_price' => $request->additional_price,
                'manufacturer_price' => $request->manufacturer_price,
                'description' => $request->description,
                'delivery_is_free' => $request->filled('delivery_is_free'),
                'has_tax_exemption' => $request->filled('has_tax_exemption'),
            ]);
            $product->setDirectory('products')
                ->setCollection(config('product.collection_gallery'))
                ->setPriority(1)
                ->addMedia($request->file('image'));
            if ($request->hasFile('model'))
                $product->setDirectory('models')
                    ->setCollection(config('product.collection_model'))
                    ->addMedia($request->file('model'));
            if ($request->has('categories_id') && is_array($request->categories_id))
                $product->categories()->sync($request->categories_id);
            if ($request->has('tags_id') && is_array($request->tags_id))
                $product->tags()->sync($request->tags_id);
            return $product;
        });
    }

    /**
     * @param $model
     * @return Model|Media
     */
    public function uploadModel($model): Model|Media
    {
        $this->model?->delete();
        return $this->setDirectory('models')
            ->setCollection(config('product.collection_model'))
            ->addMedia($model);
    }

    /**
     * Delete a product.
     *
     * @return bool|null
     */
    public function destroyProduct(): ?bool
    {
        return $this->delete();
    }

    /**
     * @return bool|null
     */
    public function deleteModel(): ?bool
    {
        return $this->model->delete();
    }

    /**
     * Update a product with valid request data.
     *
     * @param Model|Builder $product
     * @param Request $request
     * @return Model
     */
    public function updateProduct(Model|Builder $product, Request $request): Model
    {
        $product->update([
            'brand_id' => $request->brand_id,
            'unit_id' => $request->unit_id,
            'name' => $request->name,
            'slug' => $request->slug,
            'price' => $request->price,
            'old_price' => $request->old_price,
            'additional_price' => $request->additional_price,
            'manufacturer_price' => $request->manufacturer_price,
            'description' => $request->description,
            'delivery_is_free' => $request->filled('delivery_is_free'),
            'has_tax_exemption' => $request->filled('has_tax_exemption'),
        ]);
        $product->when($request->hasFile('image'), function () use ($request, $product) {
            $product->setDirectory('products')
                ->setCollection(config('product.collection_gallery'))
                ->addMedia($request->image);
        });
        $product->when($request->hasFile('model'), function () use ($product, $request) {
            $product->model()->delete();
            $product->setDirectory('models')
                ->setCollection(config('product.collection_model'))
                ->addMedia($request->model);
        });
        $product->categories()->sync($request->get('categories_id', []));
        $product->tags()->sync($request->get('tags_id', []));
        return $product->refresh()->load(['gallery', 'model']);
    }

    /**
     * @param mixed $status
     * @return Product
     */
    public function changeStatus(mixed $status): Product
    {
        $this->update(['status' => $status]);
        return $this->refresh();
    }

    /**
     * @param $user
     * @param $discount
     * @param $address
     * @return CartProductResource
     */
    public function getCartData($user, $discount = null, $address = null): CartProductResource
    {
        $cart = collect($user->cart);
        $products = self::query()
            ->select(['id', 'name', 'price', 'additional_price', 'delivery_is_free'])
            ->with(['image', 'categories:id'])
            ->whereIn('id', $cart->keys()->toArray())
            ->stock()
            ->get();
        return CartProductResource::make($products)->additional(['cart' => $cart, 'discount' => $discount, 'address' => $address]);
    }

    /**
     * @param Request $request
     * @param $products
     * @return mixed
     */
    public function getCartProductExceptIds(Request $request, $products): mixed
    {
        return self::query()
            ->select(['id', 'name', 'price'])
            ->with(['image'])
            ->when($products && count($products), function (Builder $builder) use ($products) {
                $builder->whereNotIn('id', $products);
            })->when($request->filled('search'), function (Builder $builder) use ($request) {
                $builder->where('name', 'LIKE', '%' . $request->search . '%');
            })->stock()
            ->hasStock()
            ->accepted()
            ->paginate();
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
     * @param $features
     * @return void
     */
    public function storeAttributes($features)
    {
        $attributes = collect($features)->map(function ($feature) {
            return sizeof($feature['attributes']) ? collect($feature['attributes'])->map(function ($attribute) use ($feature) {
                return [
                    'user_id' => auth()->id(),
                    'feature_id' => $feature['feature_id'],
                    'option_id' => Arr::get($attribute, 'option_id'),
                    'value' => Arr::get($attribute, 'value'),
                ];
            }) : null;
        })->flatten(1)->filter(function ($item) {
            return Arr::get($item, 'option_id') || Arr::get($item, 'value');
        })->toArray();
        $this->attributes()->delete();
        $this->attributes()->createMany($attributes);
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function mostSellingProducts(Request $request): LengthAwarePaginator
    {
        return self::query()
            ->select(['id', 'name', 'price'])
            ->with(['model', 'image'])
            ->withCount('successOrders')
            ->orderByDesc('success_orders_count')
            ->paginate($this->perPage);
    }

    /**
     * @param Request $request
     * @param $tag_ids
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getSimilarProducts(Request $request, $tag_ids): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return self::query()
            ->select(['id', 'name', 'price'])
            ->with(['model', 'image'])
            ->withCount(['tags' => function ($q) use ($tag_ids) {
                $q->whereIn('id', $tag_ids);
            }])->orderByDesc('tags_count')
            ->paginate($request->get('perPage', $this->perPage));
    }

    #endregion

    #region Relationships

    /**
     * @return MorphToMany
     */
    public function orders(): MorphToMany
    {
        return $this->morphToMany(Order::class, 'orderable', 'orderables');
    }

    /**
     * @return MorphToMany
     */
    public function successOrders(): MorphToMany
    {
        return $this->orders()->success();
    }

    public function gallery()
    {
        return $this->media()
            ->select('id', 'model_id', 'model_type', 'disk', 'files')
            ->orderByAscPriority()
            ->where('collection', config('product.collection_gallery'));
    }

    /**
     * @return MorphOne
     */
    public function image(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')
            ->select('id', 'model_id', 'model_type', 'disk', 'files')
            ->orderByAscPriority()
            ->where('collection', config('product.collection_gallery'));
    }

    /**
     * @return MorphOne
     */
    public function model(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')
            ->select('id', 'model_id', 'model_type', 'disk', 'files')
            ->where('collection', config('product.collection_model'));
    }

    /**
     * @return BelongsToMany
     */
    public function storeroom_entrances(): BelongsToMany
    {
        return $this->belongsToMany(StoreroomEntrance::class);
    }

    /**
     * @return BelongsTo
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * @return BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }

    /**
     * @return BelongsToMany
     */
    public function rack_rows(): BelongsToMany
    {
        return $this->belongsToMany(RackRow::class)->withTimestamps();
    }

    /**
     * @return HasManyDeep
     */
    public function features(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->categories()->without('parent'), (new Category())->features());
    }

    /**
     * @return HasManyDeep
     */
    public function parentFeatures(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->categories()->without('parent'), (new Category())->features())->whereNull('features.parent_id');
    }

    #endregion

    #region Scopes

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeUnitName(Builder $builder)
    {
        $builder->withAggregate('unit', 'title');
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeAccepted(Builder $builder)
    {
        $builder->where('status', ProductStatus::Accepted);
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeRejected(Builder $builder)
    {
        $builder->where('status', ProductStatus::Rejected);
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopePending(Builder $builder)
    {
        $builder->where('status', ProductStatus::Rejected);
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeWithRateAvg(Builder $builder)
    {
        $builder->withAggregate('comments as rate_avg', 'AVG(rate)');
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeWithAcceptedCommentsCount(Builder $builder)
    {
        $builder->withCount(['comments' => function (Builder $builder) {
            $builder->accepted();
        }]);
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeStock(Builder $builder)
    {
        $builder->where('products.quantity', '>=', 0);
    }

    /**
     * @param Builder $builder
     * @param $min
     * @param $max
     * @return void
     */
    public function scopeHasStock(Builder $builder, $min = null, $max = null)
    {
        $builder->when(is_numeric($min) || is_numeric($max), function (Builder $builder) use ($min, $max) {
            $builder->where(function ($builder) use ($min, $max) {
                $builder->when(is_numeric($min), function ($q) use ($min) {
                    $q->where('quantity', '>=', $min);
                })->when(is_numeric($max), function ($q) use ($max) {
                    $q->where('quantity', '<=', $max);
                });
            });
        });
    }

    #endregion

}
