<?php

namespace Modules\Feature\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Modules\Feature\Database\factories\FeatureFactory;
use Modules\Feature\Traits\HasFeature;
use Modules\Product\Entities\Product;

class Feature extends Model
{
    use HasFactory, HasFeature;

    #region Constance

    const MORPH_CLASS = "feature";

    protected $fillable = [
        'user_id',
        'parent_id',
        'featureable_id',
        'featureable_type',
        'title',
        'has_option',
        'is_filter',
    ];

    #endregion

    #region Methods

    /**
     * Init factory class.
     *
     * @return FeatureFactory
     */
    protected static function newFactory(): FeatureFactory
    {
        return FeatureFactory::new();
    }

    /**
     * Update a feature.
     *
     * @param $feature
     * @param Request $request
     * @return mixed
     */
    public function updateFeature($feature, Request $request): mixed
    {
        $feature->update([
            'parent_id' => $request->filled('parent_id') ? $request->parent_id : $feature->parent_id,
            'title' => $request->title,
            'has_option' => $request->filled('has_option') ? boolval($request->has_option) : $feature->has_option,
            'is_filter' => $request->filled('is_filter') ? boolval($request->is_filter) : $feature->is_filter,
        ]);
        return $feature->refresh();
    }

    /**
     * Store an option with options relationship.
     *
     * @param $request
     * @return Model
     */
    public function storeOption($request): Model
    {
        return $this->options()
            ->create([
                'value' => $request->option_value,
            ]);
    }

    /**
     * Find a feature where need options.
     *
     * @param $id
     * @param string $column
     * @return Model|Builder
     */
    public function findHasOptionOrFail($id, string $column = 'id'): Model|Builder
    {
        return self::query()
            ->where($column, $id)
            ->firstOrFail();
    }

    /**
     * @param $feature
     * @param string $column
     * @return bool
     */
    public function checkHasOptionByColumn($feature, string $column = 'id'): bool
    {
        return self::query()
            ->hasOption()
            ->where($column, $feature)
            ->exists();
    }

    /**
     * @param $feature
     * @param string $column
     * @return bool
     */
    public function checkDoesntHasOptionByColumn($feature, string $column = 'id'): bool
    {
        return self::query()
            ->doesntHasOption()
            ->where($column, $feature)
            ->exists();
    }

    /**
     * @param $product
     * @return Collection|array
     */
    public function productFeatures($product): Collection|array
    {
        return self::query()
            ->select(['id', 'title'])
            ->withWhereHas('children', function ($builder) use ($product) {
                $builder->select(['id', 'parent_id', 'title', 'is_filter', 'has_option'])
                    ->with('attributes', function ($builder) use ($product) {
                        $builder->where('attributable_type', Product::MORPH_CLASS)
                            ->where('attributable_id', $product->id);
                    })->with(['options' => function ($builder) {
                        $builder->select(['id', 'feature_id', 'value']);
                    }]);
            })->parent()
            ->get();
    }

    public function productCompare($products)
    {
        return self::query()
            ->select(['id', 'title'])
            ->withWhereHas('children', function ($builder) use ($products) {
                $builder->select(['id', 'parent_id', 'title', 'is_filter', 'has_option'])
                    ->withWhereHas('attributes', function ($builder) use ($products) {
                        $builder->select(['id', 'feature_id', 'attributable_type', 'attributable_id', 'value', 'option_id'])
                            ->where('attributable_type', Product::MORPH_CLASS)
                            ->whereIn('attributable_id', $products)
                            ->with(['option' => function ($builder) {
                                $builder->select('id', 'feature_id', 'value');
                            }]);
                    });
            })->parent()
            ->get();
    }

    /**
     * @return bool|null
     */
    public function destroyItem(): ?bool
    {
        return $this->delete();
    }

    #endregion

    #region Relationships

    /**
     * @return HasMany
     */
    public function options(): HasMany
    {
        return $this->hasMany(FeatureOption::class, 'feature_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * @return HasMany
     */
    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class, 'feature_id');
    }

    #endregion

    #region Scopes

    public function scopeParent(Builder $builder)
    {
        $builder->whereNull('parent_id');
    }

    public function scopeHasOption(Builder $builder)
    {
        $builder->where('has_option', true);
    }

    public function scopeDoesntHasOption(Builder $builder)
    {
        $builder->where('has_option', false);
    }

    #endregion
}
