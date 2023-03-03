<?php

namespace Modules\Rack\Entities;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Rack\Database\factories\RackFactory;
use Modules\Rack\Enums\RackStatus;

class Rack extends Model
{
    use HasFactory;

    #region Constance

    protected $fillable = [
        'user_id',
        'title',
        'url',
        'description',
        'priority',
        'status',
    ];

    protected $appends = [
        'translated_status',
        'status_css_class',
    ];

    #endregion

    #region Methods

    /**
     * @return Rack
     */
    public static function init(): Rack
    {
        return new self();
    }

    /**
     * @param array $rack_ids
     * @return void
     */
    public function changeSort(array $rack_ids)
    {
        $iteration = 0;
        foreach ($rack_ids as $rack_id) {
            self::query()->where('id', $rack_id)->update(['priority' => $iteration]);
            $iteration++;
        }
    }

    /**
     * @param Request $request
     * @return Builder[]|Collection
     */
    public function getAdminIndex(Request $request)
    {
        return self::query()
            ->orderByPriorityAsc()
            ->get();
    }

    /**
     * @return RackFactory
     */
    protected static function newFactory(): RackFactory
    {
        return RackFactory::new();
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
            'url' => $request->url,
            'description' => $request->description,
            'priority' => $this->getMaxPriority() + 1
        ]);
    }

    public function getMaxPriority()
    {
        return self::query()->max('priority') ?? 0;
    }

    /**
     * @param $rack
     * @param array $relationships
     * @return Model|Collection|Builder|array|null
     */
    public function findByIdOrFail($rack, array $relationships = []): Model|Collection|Builder|array|null
    {
        return self::query()->with($relationships)->findOrFail($rack);
    }

    /**
     * @param $status
     * @return Rack
     */
    public function changeStatus($status): Rack
    {
        $this->update(['status' => $status]);
        return $this->refresh();
    }

    /**
     * @param array $rack_row_ids
     * @return void
     */
    public function changeSortRows(array $rack_row_ids)
    {
        $iteration = 0;
        foreach ($rack_row_ids as $rack_row_id) {
            $this->rows()->where('id', $rack_row_id)->update(['priority' => $iteration]);
            $iteration++;
        }
    }

    /**
     * @param Request $request
     * @return Rack
     */
    public function updateRack(Request $request): Rack
    {
        $this->update([
            'title' => $request->title,
            'url' => $request->url,
            'description' => $request->description,
        ]);
        return $this->refresh();
    }

    /**
     * @return mixed
     */
    public function allRackRowsWithProducts()
    {
        return self::query()
            ->with(['rows' => function (HasMany $hasMany) {
                $hasMany->withWhereHas('products', function ($builder) {
                    $builder->with(['image', 'model'])
                        ->select(['products.id', 'products.name', 'products.price',])
                        ->accepted();
                })->orderByPriorityAsc()->active();
            }])->orderByPriorityAsc()
            ->accepted()
            ->get();
    }

    /**
     * @return void
     */
    public function destroyRack()
    {
        $this->delete();
    }

    #endregion

    #region Relationships

    /**
     * @return HasMany
     */
    public function rows(): HasMany
    {
        return $this->hasMany(RackRow::class, 'rack_id')->orderBy('priority');
    }

    #endregion

    #region Scopes

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeAccepted(Builder $builder)
    {
        $builder->where('status', RackStatus::Accepted);
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeRejected(Builder $builder)
    {
        $builder->where('status', RackStatus::Rejected);
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopePending(Builder $builder)
    {
        $builder->where('status', RackStatus::Pending);
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
        return RackStatus::fromValue($this->status)->getCssClass();
    }

    /**
     * @return string
     */
    public function getTranslatedStatusAttribute(): string
    {
        return RackStatus::getDescription($this->status);
    }

    #endregion
}
