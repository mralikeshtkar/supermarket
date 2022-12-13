<?php

namespace Modules\Discount\Entities;

use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\Category\Entities\Category;
use Modules\Discount\Database\factories\DiscountFactory;
use Modules\Discount\Enums\DiscountStatus;
use Modules\Product\Entities\Product;

class Discount extends Model
{
    use HasFactory;

    #region Constance

    protected $fillable = [
        'user_id',
        'code',
        'amount',
        'is_percent',
        'start_at',
        'expire_at',
        'usage_limitation',
        'uses',
        'description',
        'status',
        'priority',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'expire_at' => 'datetime',
        'is_percent' => 'boolean',
    ];

    #endregion

    #region Methods

    /**
     * Initialize class.
     *
     * @return Discount
     */
    public static function init(): Discount
    {
        return new self();
    }

    /**
     * Init factory class.
     *
     * @return DiscountFactory
     */
    protected static function newFactory(): DiscountFactory
    {
        return DiscountFactory::new();
    }

    /**
     * Store a discount.
     *
     * @param Request $request
     * @return Builder|Model
     */
    public function store(Request $request): Model|Builder
    {
        $discount = self::query()->create([
            'user_id' => $request->user()->id,
            'code' => $request->code,
            'amount' => $request->amount,
            'is_percent' => $request->filled('is_percent') ? $request->is_percent : true,
            'start_at' => $request->filled('start_at') ? Verta::parseFormat('Y/m/d H:i:s', $request->start_at) : null,
            'expire_at' => $request->filled('expire_at') ? Verta::parseFormat('Y/m/d H:i:s', $request->expire_at) : null,
            'usage_limitation' => $request->usage_limitation,
            'uses' => $request->filled('uses') ? $request->uses : 0,
            'description' => $request->description,
            'priority' => $request->priority,
        ]);
        $discount->when(Arr::get($request->discountables, 'discountable_type'), function (Builder $builder) use ($discount, $request) {
            $discount->discountables(Arr::get($request->discountables, 'discountable_type'))->sync(Arr::get($request->discountables, 'discountable_ids', []));
        });
        return $discount;
    }

    /**
     * Check code doesn't exist code in valid discounts.
     *
     * @param mixed $code
     * @return bool
     */
    public function checkDoesntExistValidDiscount(mixed $code): bool
    {
        return self::query()
            ->usageLimitationGreaterThanZeroOrNull()
            ->codeDoesntExpireOrNull()
            ->where('code', $code)
            ->doesntExist();
    }

    /**
     * Find a discount with param column.
     *
     * @param $discount
     * @param string $column
     * @return Model|Builder
     */
    public function findByColumnOrFail($discount, string $column = 'id'): Model|Builder
    {
        return self::query()->where($column, $discount)->firstOrFail();
    }

    /**
     * Update a discount.
     *
     * @param Request $request
     * @return Discount
     */
    public function updateDiscount(Request $request): Discount
    {
        $this->update([
            'code' => $request->code,
            'amount' => $request->amount,
            'is_percent' => $request->filled('is_percent') ? $request->is_percent : $this->is_percent,
            'start_at' => $request->filled('start_at') ? Verta::parseFormat('Y/m/d H:i:s', $request->start_at) : $this->start_at,
            'expire_at' => $request->filled('expire_at') ? Verta::parseFormat('Y/m/d H:i:s', $request->expire_at) : $this->expire_at,
            'usage_limitation' => $request->usage_limitation,
            'uses' => $request->filled('uses') ? $request->uses : $this->uses,
            'description' => $request->description,
            'priority' => $request->priority,
        ]);
        $this->discountables(Arr::get($request->discountables, 'discountable_type'))->sync(Arr::get($request->discountables, 'discountable_ids', []));
        return $this->refresh();
    }

    public function destroyDiscount()
    {
        $this->products()->delete();
        $this->categories()->delete();
        return $this->delete();
    }

    #endregion

    #region Relationships

    /**
     * @param string $related
     * @return MorphToMany
     */
    public function discountables(string $related): MorphToMany
    {
        return $this->morphedByMany($related, 'discountable');
    }

    /**
     * @return MorphToMany
     */
    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'discountable');
    }

    /**
     * @return MorphToMany
     */
    public function categories(): MorphToMany
    {
        return $this->morphedByMany(Category::class, 'discountable');
    }

    #endregion

    #region Scopes

    /**
     * Check code usage limitation greater than zero or null.
     *
     * @param Builder $builder
     * @return void
     */
    public function scopeUsageLimitationGreaterThanZeroOrNull(Builder $builder)
    {
        $builder->where(function (Builder $builder) {
            $builder->whereNull('usage_limitation')
                ->orWhere('usage_limitation', '>=', 0);
        });
    }

    /**
     * Check code doesnt expire or expire time is null.
     *
     * @param Builder $builder
     * @return void
     */
    public function scopeCodeDoesntExpireOrNull(Builder $builder)
    {
        $builder->where(function (Builder $builder) {
            $builder->whereNull('expire_at')->orWhere('expire_at', '>=', now());
        });
    }

    /**
     * Check code started in the past, or it is null.
     *
     * @param Builder $builder
     * @return void
     */
    public function scopeCodeIsStartedOrNull(Builder $builder)
    {
        $builder->where(function (Builder $builder) {
            $builder->whereNull('start_at')->orWhere('start_at', '<=', now());
        });
    }

    /**
     * Check code started in the past, or it is null.
     *
     * @param Builder $builder
     * @return void
     */
    public function scopeOnlyGlobalDiscount(Builder $builder)
    {
        $builder->whereNull('code');
    }

    /**
     * Check has discount condition.
     *
     * @param Builder $builder
     * @param string $morph
     * @return void
     */
    public function scopeHasDiscount(Builder $builder, string $morph)
    {
        $builder->where(function (Builder $builder) use ($morph) {
            $builder->whereNotExists(function (QueryBuilder $builder) {
                $builder->from('discountables')
                    ->whereColumn('discountables.discount_id', 'discounts.id');
            })->orWhereExists(function (QueryBuilder $builder) use ($morph) {
                $builder->from('discountables')
                    ->whereColumn('discountables.discount_id', 'discounts.id')
                    ->where('discountables.discountable_type', $morph)
                    ->whereColumn('discountables.discountable_id', 'products.id');
            })->orWhereExists(function (QueryBuilder $builder) use ($morph) {
                $builder->from('discountables')
                    ->whereColumn('discountables.discount_id', 'discounts.id')
                    ->where('discountables.discountable_type', array_search(Category::class, Relation::morphMap()))
                    ->whereExists(function (QueryBuilder $builder) use ($morph) {
                        $builder->from('categorizables')
                            ->whereColumn('categorizables.category_id', 'discountables.discountable_id')
                            ->where('categorizables.categorizables_type', $morph)
                            ->whereColumn('categorizables.categorizables_id', 'products.id');
                    });
            });
        });
    }

    /**
     * Scope where discount status is accepted.
     *
     * @param Builder $builder
     * @return void
     */
    public function scopeAccepted(Builder $builder)
    {
        $builder->where('status', DiscountStatus::Accepted);
    }

    public function scopeOrderPriority(Builder $builder)
    {
        $builder->orderByDesc('priority');
    }

    #endregion

}
