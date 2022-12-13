<?php

namespace Modules\Feature\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaravelIdea\Helper\Modules\Feature\Entities\_IH_FeatureOption_C;
use LaravelIdea\Helper\Modules\Feature\Entities\_IH_FeatureOption_QB;
use Modules\Feature\Database\factories\FeatureOptionFactory;

class FeatureOption extends Model
{
    use HasFactory;

    #region Constance

    protected $fillable = [
        'feature_id',
        'value',
    ];

    #endregion

    #region Methods

    /**
     * @return FeatureOption
     */
    public static function init(): FeatureOption
    {
        return new self();
    }

    /**
     * @param $option
     * @param $feature
     * @return Model|Collection|_IH_FeatureOption_QB|array|Builder|FeatureOption|_IH_FeatureOption_C|null
     */
    public function findOrFailByIdAndFeatureId($option, $feature): Model|Collection|_IH_FeatureOption_QB|array|Builder|FeatureOption|_IH_FeatureOption_C|null
    {
        return self::query()->where('feature_id', $feature)->findOrFail($option);
    }

    /**
     * @return bool|null
     */
    public function destroyItem(): ?bool
    {
        return $this->delete();
    }

    /**
     * Init factory class.
     *
     * @return FeatureOptionFactory
     */
    protected static function newFactory(): FeatureOptionFactory
    {
        return FeatureOptionFactory::new();
    }

    #endregion

    #region Relationships

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }

    #endregion
}
