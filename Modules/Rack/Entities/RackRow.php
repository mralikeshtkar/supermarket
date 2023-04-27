<?php

namespace Modules\Rack\Entities;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Modules\Product\Entities\Product;
use Modules\Rack\Database\factories\RackRowFactory;
use Modules\Rack\Enums\RackRowStatus;
use Modules\User\Entities\User;
use Staudenmeir\EloquentHasManyDeep\Eloquent\Relations\Traits\HasEagerLimit;

class RackRow extends Model
{
    use HasFactory,HasEagerLimit;

    #region Constance

    protected $fillable = [
        'user_id',
        'rack_id',
        'title',
        'priority',
        'status',
        'number_limit',
    ];

    /*protected $appends = [
        'translated_status',
        'status_css_class',
    ];*/

    #endregion

    #region Methods

    protected static function boot()
    {
        parent::boot();
        static::updated(function (RackRow $rackRow) {
            if ($rackRow->wasChanged('number_limit')) {
                $rackRow->products()->sync([]);
            }
        });
    }

    /**
     * @return RackRow
     */
    public static function init(): RackRow
    {
        return new self();
    }

    /**
     * @return RackRowFactory
     */
    protected static function newFactory(): RackRowFactory
    {
        return RackRowFactory::new();
    }

    /**
     * @param mixed $rack
     * @param Request $request
     * @return mixed
     */
    public function store(mixed $rack, Request $request)
    {
        return $rack->rows()->create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'number_limit' => $request->number_limit,
            'priority' => $this->getMaxPriority($rack) + 1,
        ]);
    }

    public function getMaxPriority($rack)
    {
        return $rack->rows()->max('priority') ?? 0;
    }

    /**
     * @param $rack_row
     * @param array $relationships
     * @return Model|Collection|Builder|array|null
     */
    public function findByIdOrFail($rack_row, array $relationships = []): Model|Collection|Builder|array|null
    {
        return self::query()->with($relationships)->findOrFail($rack_row);
    }

    /**
     * @return void
     */
    public function destroyRackRow()
    {
        $this->delete();
    }

    /**
     * @param array $sorts
     * @return void
     */
    public function changeSortProducts(array $sorts)
    {
        $priority = 0;
        foreach ($sorts as $sort) {
            $this->products()
                ->withPivot(['priority'])
                ->wherePivot('id', $sort['id'])
                ->updateExistingPivot($sort['product_id'], ['priority' => $priority]);
            $priority++;
        }
    }

    /**
     * @param Request $request
     * @return RackRow
     */
    public function updateRackRow(Request $request): RackRow
    {
        $this->update([
            'title' => $request->title,
            'number_limit' => $request->number_limit,
        ]);
        return $this->refresh();
    }

    /**
     * @param $status
     * @return RackRow
     */
    public function updateStatus($status): RackRow
    {
        $this->update(['status' => $status]);
        return $this->refresh();
    }

    /**
     * @param Request $request
     * @return RackRow
     */
    public function attachProduct(Request $request): RackRow
    {
        $this->products()->attach($request->product_id, ['user_id' => $request->user()->id, 'priority' => $this->products()->max('product_rack_row.priority') + 1]);
        return $this->refresh()->load(['products', 'products.image']);
    }

    /**
     * @param $product_id
     * @return RackRow
     */
    public function detachProduct($product_id): RackRow
    {
        $this->products()->detach($product_id);
        return $this->refresh()->load(['products', 'products.image']);
    }

    /**
     * @param mixed $rack_row_id
     * @param mixed $product_id
     * @return bool
     */
    public function productExistsInRackRow(mixed $rack_row_id, mixed $product_id): bool
    {
        return self::query()
            ->where('id', $rack_row_id)
            ->whereHas('products', function (Builder $builder) use ($product_id) {
                $builder->where('id', $product_id)->accepted();
            })->exists();
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
    public function rack(): BelongsTo
    {
        return $this->belongsTo(Rack::class, 'rack_id');
    }

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('id')
            ->orderByPivot('priority');
    }

    #endregion

    #region Scopes

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeActive(Builder $builder)
    {
        $builder->where('status', RackRowStatus::Active);
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeInactive(Builder $builder)
    {
        $builder->where('status', RackRowStatus::Inactive);
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeOrderByPriorityAsc(Builder $builder)
    {
        $builder->orderBy('priority');
    }

    #endregion

    #region Mutators

    /**
     * @return array|string|Translator|Application|null
     */
    public function getStatusCssClassAttribute(): array|string|Translator|Application|null
    {
        return RackRowStatus::fromValue($this->status)->getCssClass();
    }

    /**
     * @return string
     */
    public function getTranslatedStatusAttribute(): string
    {
        return RackRowStatus::getDescription($this->status);
    }

    #endregion

}
