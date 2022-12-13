<?php

namespace Modules\Storeroom\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Modules\Product\Entities\Product;
use Modules\Storeroom\Database\factories\StoreroomOutEntranceFactory;

class StoreroomOutEntrance extends Model
{
    use HasFactory;

    #region Constance

    protected $fillable = [
        'storeroom_out_id',
        'storeroom_entrance_id',
    ];

    #endregion

    #region Methods

    /**
     * @return StoreroomOutEntrance
     */
    public static function init(): StoreroomOutEntrance
    {
        return new self();
    }

    /**
     * @return StoreroomOutEntranceFactory
     */
    protected static function newFactory(): StoreroomOutEntranceFactory
    {
        return StoreroomOutEntranceFactory::new();
    }

    /**
     * @param $storeroom_out_entrance
     * @return Model|Collection|Builder|array|null
     */
    public function findByIdOrFail($storeroom_out_entrance): Model|Collection|Builder|array|null
    {
        return self::query()->findOrFail($storeroom_out_entrance);
    }

    /**
     * @param Request $request
     * @return StoreroomOutEntrance
     */
    public function updateProducts(Request $request): StoreroomOutEntrance
    {
        $this->products()->sync(collect($request->get('products', []))->mapWithKeys(fn($item, $key) => [
            $item['product_id'] => [
                'quantity' => $item['quantity'],
            ],
        ]));
        return $this->load('products', 'products.gallery', 'products.model');
    }

    #endregion

    #region Relationships

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * @return BelongsTo
     */
    public function storeroomEntrance(): BelongsTo
    {
        return $this->belongsTo(StoreroomEntrance::class);
    }

    #endregion
}
