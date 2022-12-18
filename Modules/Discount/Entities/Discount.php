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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Modules\Category\Entities\Category;
use Modules\Discount\Database\factories\DiscountFactory;
use Modules\Discount\Enums\DiscountStatus;
use Modules\Discount\Exceptions\DiscountIsInvalidException;
use Modules\Product\Entities\Product;
use Symfony\Component\HttpFoundation\Response;

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
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'expire_at' => 'datetime',
        'is_percent' => 'boolean',
    ];

    private array $selected_columns = ['*'];

    private array $with_relationships = [];

    private array $with_scopes = [];

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
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAdminIndexPaginate(Request $request): LengthAwarePaginator
    {
        return self::query()
            ->select(['id', 'code', 'status', 'amount', 'is_percent', 'start_at', 'expire_at', 'usage_limitation', 'uses', 'description',])
            ->with(['products:id', 'categories:id'])
            ->when($request->filled('code'), function (Builder $builder) use ($request) {
                $builder->where('code', 'LIKE', '%' . $request->code . '%');
            })->latest()
            ->paginate();
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
            'is_percent' => $request->get('is_percent',true),
            'start_at' => $request->filled('start_at') ? Verta::parseFormat('Y/n/j H:i', $request->start_at) : null,
            'expire_at' => $request->filled('expire_at') ? Verta::parseFormat('Y/n/j H:i', $request->expire_at) : null,
            'usage_limitation' => $request->usage_limitation,
            'uses' => $request->filled('uses') ? $request->uses : 0,
            'description' => $request->description,
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
     * @param $discount
     * @return bool
     */
    public function checkDoesntExistValidDiscount(mixed $code, $discount = null): bool
    {
        return self::query()
            ->when(!is_null($discount), function (Builder $builder) use ($discount) {
                $builder->whereNot('id', $discount);
            })->usageLimitationGreaterThanZeroOrNull()
            ->codeDoesntExpireOrNull()
            ->where('code', $code)
            ->doesntExist();
    }

    /**
     * @param $discount
     * @return mixed
     */
    public function findOrFailById($discount): mixed
    {
        return self::query()->select($this->selected_columns)
            ->with($this->with_relationships)
            ->scopes($this->with_scopes)
            ->findOrFail($discount);
    }

    /**
     * @param $code
     * @return mixed
     * @throws DiscountIsInvalidException
     */
    public function findValidDiscountByCode($code): mixed
    {
        $discount = self::query()->select($this->selected_columns)
            ->with($this->with_relationships)
            ->scopes($this->with_scopes)
            ->where('code', $code)
            ->usageLimitationGreaterThanZeroOrNull()
            ->codeIsStartedOrNull()
            ->codeDoesntExpireOrNull()
            ->accepted()
            ->orderByAmountDesc()
            ->orderByIsPercentDesc()
            ->first();
        if ($discount) return $discount;
        else throw new DiscountIsInvalidException(trans("Discount is invalid"), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param $discount
     * @return mixed
     */
    public function changeStatus($discount): mixed
    {
        $this->update(['status' => DiscountStatus::coerce($discount->status)->is(DiscountStatus::Accepted) ? DiscountStatus::Rejected : DiscountStatus::Accepted]);
        return $this->selectColumns(['id', 'code', 'status', 'amount', 'is_percent', 'start_at', 'expire_at', 'usage_limitation', 'uses', 'description',])
            ->findOrFailById($this->id);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function updateDiscount(Request $request)
    {
        $this->update([
            'code' => $request->code,
            'amount' => $request->amount,
            'is_percent' => $request->filled('is_percent') ? $request->is_percent : $this->is_percent,
            'start_at' => $request->filled('start_at') ? Verta::parseFormat('Y/m/d H:i', $request->start_at) : $this->start_at,
            'expire_at' => $request->filled('expire_at') ? Verta::parseFormat('Y/m/d H:i', $request->expire_at) : $this->expire_at,
            'usage_limitation' => $request->usage_limitation,
            'uses' => $request->filled('uses') ? $request->uses : $this->uses,
            'description' => $request->description,
        ]);
        $this->products()->sync([]);
        $this->categories()->sync([]);
        if (Arr::get($request->discountables, 'discountable_type')) {
            $this->discountables(Arr::get($request->discountables, 'discountable_type'))->sync(Arr::get($request->discountables, 'discountable_ids', []));
        }
    }

    /**
     * @return bool|null
     */
    public function destroyDiscount(): ?bool
    {
        $this->products()->delete();
        $this->categories()->delete();
        return $this->delete();
    }

    /**
     * @return string
     */
    public function getTranslatedStatus(): string
    {
        return DiscountStatus::getDescription($this->status);
    }

    /**
     * @return string
     */
    public function getStatusClassName(): string
    {
        return DiscountStatus::coerce($this->status)->getCssClass();
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
                ->orWhereColumn('usage_limitation', '>', 'uses');
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

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeOrderByAmountDesc(Builder $builder)
    {
        $builder->orderByDesc('amount');
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeOrderByIsPercentDesc(Builder $builder)
    {
        $builder->orderByDesc('is_percent');
    }

    #endregion

}
