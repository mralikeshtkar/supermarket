<?php

namespace Modules\Storeroom\Entities;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Product\Entities\Product;
use Modules\Storeroom\Database\factories\StoreroomEntranceFactory;
use Modules\User\Entities\User;
use Throwable;

class StoreroomEntrance extends Model
{
    use HasFactory;

    #region Constance

    protected $fillable = [
        'user_id',
        'storeroom_id',
    ];

    protected $appends = [
        'created_at_fa'
    ];

    #endregion

    #region Methods

    /**
     * @param $storeroom_entrance
     * @return Model|Collection|Builder|array|null
     */
    public function findByIdOrFail($storeroom_entrance): Model|Collection|Builder|array|null
    {
        return self::query()->findOrFail($storeroom_entrance);
    }

    /**
     * @param array $products
     * @return mixed
     * @throws Throwable
     */
    public function updateProduct(array $products): mixed
    {
        return DB::transaction(function () use ($products) {
            foreach ($products as $product) {
                $productItem = $this->products()->findOrFail($product['id']);
                $productItem->update(['quantity' => $productItem->quantity > $productItem->pivot->quantity ? $productItem->quantity - $productItem->pivot->quantity : 0]);
                $this->products()->updateExistingPivot($product['id'], ['quantity' => $product['quantity'], 'price' => $product['price'],]);
                $productItem->increment('quantity', $product['quantity']);
            }
        });
    }

    /**
     * @param $product_id
     * @return void
     */
    public function deleteProduct($product_id)
    {
        $product = $this->products()->findOrFail($product_id);
        $product->update(['quantity' => $product->quantity > $product->pivot->quantity ? $product->quantity - $product->pivot->quantity : 0]);
        $this->products()->detach($product_id);
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function paginateProducts(Request $request): LengthAwarePaginator
    {
        return $this->products()->with('image')->paginate();
    }

    /**
     * @param Request $request
     * @return StoreroomEntrance
     */
    public function updateStoreroomEntrance(Request $request): StoreroomEntrance
    {
        $this->products()->sync($this->_convertedProductGroup($request));
        return $this->refresh()->load([
            'user:id,name,mobile',
            'storeroom:id,name',
            'products' => function (BelongsToMany $belongsToMany) {
                $belongsToMany->with(['gallery', 'model']);
            },
        ]);
    }

    /**
     * @return StoreroomEntrance
     */
    public static function init(): StoreroomEntrance
    {
        return new self();
    }

    public function productExistsInEntrance($entrance_id, $product_id): bool
    {
        return self::query()
            ->whereHas('products', function (Builder $builder) use ($product_id) {
                $builder->where('id', $product_id);
            })->where('id', $entrance_id)
            ->exists();
    }

    /**
     * @return StoreroomEntranceFactory
     */
    protected static function newFactory(): StoreroomEntranceFactory
    {
        return StoreroomEntranceFactory::new();
    }

    /**
     * @param Request $request
     * @return mixed[]
     */
    private function _convertedProductGroup(Request $request): array
    {
        return collect($request->get('products'))->mapWithKeys(function ($item, $key) {
            return [
                $item['id'] => [
                    'quantity' => Arr::get($item, 'quantity'),
                    'price' => $item['price'],
                ],
            ];
        })->toArray();
    }

    #endregion

    #region Relationships

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function storeroom(): BelongsTo
    {
        return $this->belongsTo(Storeroom::class, 'storeroom_id');
    }

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_storeroom_entrance', '', '', '')
            ->withPivot(['quantity', 'price']);
    }

    /**
     * @param $storeroom
     * @param Request $request
     * @return mixed
     * @throws Throwable
     */
    public function store($storeroom, Request $request): mixed
    {
        return DB::transaction(function () use ($storeroom, $request) {
            $storeroom_entrance = $storeroom->entrances()->create([
                'user_id' => $request->user()->id,
            ]);
            $products = $this->_convertedProductGroup($request);
            $storeroom_entrance->products()->attach($products);
            foreach ($products as $productId => $item) {
                Product::query()->where('id', $productId)->increment('quantity', $item['quantity']);
            }
            return $storeroom_entrance->load([
                'user:id,name,mobile',
                'storeroom:id,name',
                'products' => function (BelongsToMany $belongsToMany) {
                    $belongsToMany->with(['gallery', 'model']);
                },
            ]);
        });
    }

    #endregion

    #region Mutators

    /**
     * @return string
     */
    public function getCreatedAtFaAttribute(): string
    {
        return verta($this->created_at)->formatJalaliDate();
    }

    #endregion
}
