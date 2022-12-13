<?php

namespace Modules\Address\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use LaravelIdea\Helper\Modules\Address\Entities\_IH_Province_C;
use Modules\Address\Database\factories\ProvinceFactory;

class Province extends Model
{
    use HasFactory;

    #region Constance

    protected $fillable = [
        'name',
    ];

    #endregion

    #region Methods

    /**
     * Initialize class.
     *
     * @return Province
     */
    public static function init(): Province
    {
        return new self();
    }

    /**
     * @return Collection|array|_IH_Province_C
     */
    public function getProvinces(): Collection|array|_IH_Province_C
    {
        return self::query()->get();
    }

    /**
     * Store a province in database.
     *
     * @param Request $request
     * @return Builder|Model
     */
    public function store(Request $request): Model|Builder
    {
        return self::query()->create([
            'name' => $request->name,
        ]);
    }

    /**
     * Init factory class.
     *
     * @return ProvinceFactory
     */
    protected static function newFactory(): ProvinceFactory
    {
        return ProvinceFactory::new();
    }

    /**
     * Find a province with parameter column.
     *
     * @param $id
     * @param $column
     * @return Model|Builder
     */
    public function findByColumnOrFail($id, $column = 'id'): Model|Builder
    {
        return self::query()
            ->where($column, $id)
            ->firstOrFail();
    }

    /**
     * Update a province.
     *
     * @param Request $request
     * @return Province
     */
    public function updateProvince(Request $request): Province
    {
        $this->update([
            'name' => $request->name,
        ]);
        return $this->refresh();
    }

    /**
     * Delete a province.
     *
     * @return bool|null
     */
    public function destroyProvince(): ?bool
    {
        return $this->delete();
    }

    #endregion

    #region Relationships

    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'province_id');
    }

    #endregion

}
