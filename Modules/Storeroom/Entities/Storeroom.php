<?php

namespace Modules\Storeroom\Entities;

use Hekmatinasser\Verta\Verta;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Http\Request;
use Modules\Address\Entities\City;
use Modules\Address\Entities\Province;
use Modules\Product\Entities\Product;
use Modules\Storeroom\Database\factories\StoreroomFactory;
use Modules\User\Entities\User;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Storeroom extends Model
{
    use HasFactory,HasRelationships;

    #region Constance

    protected $fillable = [
        'user_id',
        'province_id',
        'city_id',
        'name',
        'address',
        'phone_numbers',
        'lat',
        'lng',
    ];

    protected $casts = [
        'phone_numbers' => 'array',
    ];

    protected $appends = [
        'created_at_fa',
    ];

    #endregion

    #region Methods

    /**
     * @return Storeroom
     */
    public static function init(): Storeroom
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
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'name' => $request->name,
            'address' => $request->address,
            'phone_numbers' => $request->phone_numbers,
            'lat' => $request->lat,
            'lng' => $request->lng,
        ]);
    }

    /**
     * @return StoreroomFactory
     */
    protected static function newFactory(): StoreroomFactory
    {
        return StoreroomFactory::new();
    }

    /**
     * @param $storeroom
     * @param array $relationships
     * @return Model|Collection|Builder|array|null
     */
    public function findByIdOrFail($storeroom, array $relationships = []): Model|Collection|Builder|array|null
    {
        return self::query()
            ->with($relationships)
            ->withAggregate('province', 'name')
            ->withAggregate('city', 'name')
            ->findOrFail($storeroom);
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAdminEntrancePaginate(Request $request): LengthAwarePaginator
    {
        return $this->entrances()
            ->with('user')
            ->withCount('products')
            ->when($request->filled('code'), function (Builder $builder) use ($request) {
                $builder->where('id', 'LIKE', '%' . $request->code . '%');
            })->when($request->filled('from') && validateDate($request->from), function (Builder $builder) use ($request) {
                $builder->whereDate('created_at', '>=', Verta::parseFormat('Y/m/d', $request->from)->datetime());
            })->when($request->filled('to') && validateDate($request->to), function (Builder $builder) use ($request) {
                $builder->whereDate('created_at', '<=', Verta::parseFormat('Y/m/d', $request->to)->datetime());
            })->when($request->filled('product'), function (Builder $builder) use ($request) {
                $builder->whereHas('products', function (Builder $builder) use ($request) {
                    $builder->where(function (Builder $builder) use ($request) {
                        $builder->where('name', 'LIKE', '%' . $request->product . '%')
                            ->orWhere('slug', 'LIKE', '%' . $request->product . '%');
                    });
                });
            })->latest()
            ->paginate(2)
            ->appends($request->only(['code', 'from', 'to', 'product']));
    }

    /**
     * @param Request $request
     * @return Storeroom
     */
    public function updateStoreroom(Request $request): Storeroom
    {
        $this->update([
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'name' => $request->name,
            'address' => $request->address,
            'phone_numbers' => $request->phone_numbers,
            'lat' => $request->lat,
            'lng' => $request->lng,
        ]);
        return $this->refresh();
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAdminIndexPaginate(Request $request): LengthAwarePaginator
    {
        return self::query()
            ->withAggregate('province', 'name')
            ->withAggregate('city', 'name')
            ->when($request->filled('name'), function (Builder $builder) use ($request) {
                $builder->where('name', 'LIKE', '%' . $request->name . '%');
            })
            ->when($request->filled('date') && validateDate($request->date), function (Builder $builder) use ($request) {
                $builder->whereDate('created_at', Verta::parseFormat('Y/m/d', $request->date)->datetime());
            })->latest()
            ->paginate()
            ->appends($request->only(['search', 'date']));
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getProducts(Request $request): LengthAwarePaginator
    {
        return $this->products()
            ->paginate();
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
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    /**
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * @return HasMany
     */
    public function entrances(): HasMany
    {
        return $this->hasMany(StoreroomEntrance::class, 'storeroom_id');
    }

    public function products()
    {
        return $this->hasManyDeepFromRelations($this->entrances(),(new StoreroomEntrance())->products())
            ->with('image')
            ->withPivot('product_storeroom_entrance',['*']);
    }

    #endregion

    #region Mutators

    /**
     * @return string
     */
    public function getCreatedAtFaAttribute()
    {
        return verta($this->created_at)->formatJalaliDate();
    }

    #endregion
}
