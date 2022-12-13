<?php

namespace Modules\Feature\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Feature\Database\factories\AttributeFactory;

class Attribute extends Model
{
    use HasFactory;

    #region Constance

    protected $fillable = [
        'user_id',
        'feature_id',
        'option_id',
        'attributable_id',
        'attributable_type',
        'value',
    ];

    #endregion

    #region Methods

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('scopeOptionName',function (Builder $builder){
           $builder->optionValue();
        });
    }

    /**
     * Init factory class.
     *
     * @return AttributeFactory
     */
    protected static function newFactory(): AttributeFactory
    {
        return AttributeFactory::new();
    }

    #endregion

    #region Relationships

    /**
     * @return MorphTo
     */
    public function attributable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasOne
     */
    public function option(): HasOne
    {
        return $this->hasOne(FeatureOption::class,'id','option_id');
    }

    #endregion

    #region Scopes

    public function scopeOptionValue(Builder $builder)
    {
        $builder->withAggregate('option As option_value','value');
    }

    #endregion
}
