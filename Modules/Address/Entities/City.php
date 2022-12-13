<?php

namespace Modules\Address\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Modules\Address\Database\factories\CityFactory;

class City extends Model
{
    use HasFactory;

    #region Constance

    protected $fillable = [
        'province_id',
        'name',
    ];

    #endregion

    #region Methods

    /**
     * Initialize class.
     *
     * @return City
     */
    public static function init(): City
    {
        return new self();
    }

    /**
     * Init factory class.
     *
     * @return CityFactory
     */
    protected static function newFactory(): CityFactory
    {
        return CityFactory::new();
    }

    /**
     * Store a city.
     *
     * @param Request $request
     * @return Model|Builder
     */
    public function store(Request $request): Model|Builder
    {
        return self::query()->create([
            'province_id' => $request->province_id,
            'name' => $request->name,
        ]);
    }

    /**
     * Find a city with param column.
     *
     * @param $id
     * @param $column
     * @return Model|Builder
     */
    public function findByColumnOrFail($id, $column='id'): Model|Builder
    {
        return self::query()
            ->where($column,$id)
            ->firstOrFail();
    }

    /**
     * @param Request $request
     * @return City
     */
    public function updateCity(Request $request): City
    {
        $this->update([
            'province_id' => $request->province_id,
            'name' => $request->name,
        ]);
        return $this->refresh();
    }

    /**
     * Destroy a city.
     *
     * @return bool|null
     */
    public function destroyCity(): ?bool
    {
        return $this->delete();
    }

    #endregion

    #region Relationships

    public function province():BelongsTo
    {
        return $this->belongsTo(Province::class,'province_id');
    }

    #endregion
}
