<?php

namespace Modules\Storeroom\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Modules\Product\Entities\Product;
use Modules\Storeroom\Database\factories\StoreroomOutFactory;

class StoreroomOut extends Model
{
    use HasFactory;

    #region Constance

    protected $fillable = [
        'user_id',
    ];

    #endregion

    #region Methods

    /**
     * @return StoreroomOut
     */
    public static function init(): StoreroomOut
    {
        return new self();
    }

    /**
     * @return StoreroomOutFactory
     */
    protected static function newFactory(): StoreroomOutFactory
    {
        return StoreroomOutFactory::new();
    }

    /**
     * @param Request $request
     * @return Model|Builder
     */
    public function store(Request $request): Model|Builder
    {
        return self::query()->create([
            'user_id' => $request->user()->id,
        ]);
    }

    #endregion

    #region Relationships

    /**
     * @return HasMany
     */
    public function storeroomOutEntrances(): HasMany
    {
        return $this->hasMany(StoreroomOutEntrance::class);
    }

    #endregion
    public function storeProduct(Request $request)
    {
        $this->products()->attach(collect($request->products)->mapWithKeys(fn($item, $key) => [
            $item['product_id'] => [
                'storeroom_entrance_id' => $item['storeroom_entrance_id'],
                'quantity' => $item['product_id'],
            ]
        ]));
    }

}
