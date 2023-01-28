<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Address\Entities\City;
use Staudenmeir\EloquentHasManyDeep\HasOneDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class OrderAddress extends Model
{
    use HasFactory,HasRelationships;

    #region Constance

    /**
     * @var string[]
     */
    protected $fillable = [
        'order_id',
        'city_id',
        'name',
        'mobile',
        'address',
        'postal_code',
        'type',
    ];

    #endregion

    #region Relations

    /**
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return HasOneDeep
     */
    public function province(): HasOneDeep
    {
        return $this->hasOneDeepFromRelations($this->city(),(new City())->province());
    }

    #endregion

}
