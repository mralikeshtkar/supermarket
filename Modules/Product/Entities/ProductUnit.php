<?php

namespace Modules\Product\Entities;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Modules\Product\Database\factories\ProductUnitFactory;
use Modules\Product\Enums\ProductUnitStatus;

class ProductUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'status',
    ];

    protected $appends = [
        'translated_status',
        'status_css_class',
    ];

    #region Methods

    /**
     * @return ProductUnitFactory
     */
    protected static function newFactory(): ProductUnitFactory
    {
        return ProductUnitFactory::new();
    }

    /**
     * @return ProductUnit
     */
    public static function init(): ProductUnit
    {
        return new self();
    }

    /**
     * @param $productUnit
     * @return Model|Collection|Builder|array|null
     */
    public function findOrFailById($productUnit): Model|Collection|Builder|array|null
    {
        return self::query()->findOrFail($productUnit);
    }

    /**
     * @return mixed
     */
    public function destroyUnit(): mixed
    {
        return self::query()->where('id',$this->id)->delete();
    }

    /**
     * @param $status
     * @return ProductUnit
     */
    public function changeStatus($status): ProductUnit
    {
        $this->update(['status' => $status]);
        return $this->refresh();
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAdminIndexPaginate(Request $request): LengthAwarePaginator
    {
        return self::query()
            ->when($request->filled('title'), function (Builder $builder) use ($request) {
                $builder->where('title', 'LIKE', '%' . $request->title . '%');
            })->paginate()
            ->appends($request->only('title'));
    }

    /**
     * @param Request $request
     * @return Model|Builder
     */
    public function store(Request $request): Model|Builder
    {
        return self::query()->create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'status' => $request->filled('status')
                ? $request->status
                : ProductUnitStatus::Pending,
        ]);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function updateUnit(Request $request)
    {
        $this->update([
            'title' => $request->title,
            'status' => $request->filled('status')
                ? $request->status
                : ProductUnitStatus::Pending,
        ]);
    }

    /**
     * @return mixed
     */
    public function delete(): mixed
    {
        return $this->delete();
    }

    /**
     * @return mixed
     */
    public function onlyAccepted()
    {
        return self::query()
            ->select('id','title','status')
            ->accepted()
            ->get();
    }

    #endregion

    #region Mutators

    /**
     * @return string
     */
    public function getTranslatedStatusAttribute(): string
    {
        return ProductUnitStatus::getDescription($this->status);
    }

    public function getStatusCssClassAttribute()
    {
        return ProductUnitStatus::fromValue($this->status)->getCssClass();
    }

    #endregion

    #region Scopes

    public function scopeAccepted(Builder $builder)
    {
        $builder->where('status', ProductUnitStatus::Accepted);
    }

    #endregion
}
