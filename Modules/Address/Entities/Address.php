<?php

namespace Modules\Address\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Modules\Address\Database\factories\AddressFactory;
use Modules\User\Entities\User;

class Address extends Model
{
    use HasFactory;

    #region Constance

    protected $fillable = [
        'user_id',
        'province_id',
        'city_id',
        'name',
        'mobile',
        'address',
        'postal_code',
        'latitude',
        'longitude',
    ];

    #endregion

    #region Methods

    /**
     * Initialize class.
     *
     * @return Address
     */
    public static function init(): Address
    {
        return new self();
    }

    public function getUserAddresses(Request $request)
    {
        return $request->user()->addresses()->latest()->paginate();
    }

    /**
     * Store an address.
     *
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
            'mobile' => to_valid_mobile_number($request->mobile),
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
    }

    /**
     * Init factory class.
     *
     * @return AddressFactory
     */
    protected static function newFactory(): AddressFactory
    {
        return AddressFactory::new();
    }

    /**
     * Find an address with param column.
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
     * Destroy an address;
     *
     * @return bool|null
     */
    public function destroyAddress(): ?bool
    {
        return $this->delete();
    }

    /**
     * Update an address.
     *
     * @param Request $request
     * @return Address
     */
    public function updateAddress(Request $request): Address
    {
        $this->update([
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'name' => $request->name,
            'mobile' => to_valid_mobile_number($request->mobile),
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
        return $this->refresh();
    }

    #endregion

    #region Relationships

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    #endregion

}
