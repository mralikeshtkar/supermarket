<?php

namespace Modules\Product\Entities;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Traits\EloquentHelper;
use Modules\Setting\Entities\Setting;
use Modules\User\Entities\User;

class Special extends Model
{
    use HasFactory, EloquentHelper;

    #region Constance

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'priority',
    ];

    #endregion

    #region Relations

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    #endregion

    #region Methods

    /**
     * @return Special
     */
    public static function init(): Special
    {
        return new self();
    }

    /**
     * @param Request $request
     * @return Model|Builder
     */
    public function store(Request $request): Model|Builder
    {
        return self::query()->create([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
            'priority' => $this->getMaximumPriority() + 1,
        ]);
    }

    /**
     * @param Request $request
     * @param $product
     * @return Model|Builder
     */
    public function addProduct(Request $request, $product): Model|Builder
    {
        return self::query()->create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'priority' => $this->getMaximumPriority() + 1,
        ]);
    }

    public function getMaximumPriority()
    {
        return self::query()->max('priority') ?? 0;
    }

    /**
     * @param array $specials
     * @return void
     */
    public function chartSort(array $specials)
    {
        $priority = 1;
        foreach ($specials as $special) {
            self::query()->where('id', $special)->update(['priority' => $priority]);
            $priority++;
        }
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAdminIndexPaginate(Request $request): LengthAwarePaginator
    {
        return self::query()
            ->with(['product', 'product.image'])
            ->select(['id', 'product_id', 'priority'])
            ->orderByPriority()
            ->paginate();
    }

    /**
     * @param Request $request
     * @return Collection|array
     */
    public function getIndex(Request $request): Collection|array
    {
        return self::query()
            ->select(['priority', 'product_id'])
            ->with(['product:id,name,price', 'product.image', 'product.model'])
            ->has('product')
            ->limit(Cache::get(Setting::SETTING_CACHE_KEY,collect())->get(Setting::SETTING_SPECIAL_PRODUCTS_LIMIT, 18))
            ->get();
    }

    #endregion

    #region Scopes

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeOrderByPriority(Builder $builder)
    {
        $builder->orderBy('priority');
    }

    #endregion

}
