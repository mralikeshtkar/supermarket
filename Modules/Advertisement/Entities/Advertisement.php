<?php

namespace Modules\Advertisement\Entities;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Http\Request;
use LaravelIdea\Helper\Modules\Brand\Entities\_IH_Brand_C;
use LaravelIdea\Helper\Modules\Brand\Entities\_IH_Brand_QB;
use Modules\Advertisement\Enums\AdvertisementStatus;
use Modules\Brand\Entities\Brand;
use Modules\Media\Traits\HasMedia;

class Advertisement extends Model
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'user_id',
        "place",
        "status",
    ];

    private array $selected_columns = ['*'];

    private array $with_relationships = [];

    private array $with_scopes = [];

    const MEDIA_COLLECTION_ADVERTISEMENTS = "advertisements";

    const MEDIA_DIRECTORY_ADVERTISEMENTS = "advertisements";

    #region Methods

    /**
     * @return Advertisement
     */
    public static function init(): Advertisement
    {
        return new self();
    }

    /**
     * @param Request $request
     * @return Model|Builder
     */
    public function store(Request $request): Model|Builder
    {
        $advertisement = self::query()->create([
            'user_id' => $request->user()->id,
            'place' => $request->place,
            'status' => $request->status,
        ]);
        $advertisement->setCollection(self::MEDIA_COLLECTION_ADVERTISEMENTS)
            ->setDirectory(self::MEDIA_DIRECTORY_ADVERTISEMENTS)
            ->addMedia($request->image);
        return $advertisement;
    }

    /**
     * @param Request $request
     * @return void
     */
    public function updateRow(Request $request)
    {
        $this->update([
            'place' => $request->place,
            'status' => $request->status,
        ]);
        if ($request->hasFile('image')) {
            $this->removeAllMedia(self::MEDIA_COLLECTION_ADVERTISEMENTS)
                ->setCollection(self::MEDIA_COLLECTION_ADVERTISEMENTS)
                ->setDirectory(self::MEDIA_DIRECTORY_ADVERTISEMENTS)
                ->addMedia($request->image);
        }
    }

    public function destroyRow()
    {
        $this->removeAllMedia(self::MEDIA_COLLECTION_ADVERTISEMENTS);
        $this->delete();
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function paginateAdmin(Request $request): LengthAwarePaginator
    {
        return self::query()->select($this->selected_columns)
            ->with($this->with_relationships)
            ->scopes($this->with_scopes)
            ->paginate($request->get('perPage', 10));
    }

    /**
     * @param $advertisement
     * @return Model|Collection|array|Builder|Brand|_IH_Brand_C|_IH_Brand_QB|null
     */
    public function findOrFailById($advertisement): Model|Collection|array|Builder|Brand|_IH_Brand_C|_IH_Brand_QB|null
    {
        return self::query()
            ->with($this->with_relationships)
            ->select($this->selected_columns)
            ->scopes($this->with_scopes)
            ->findOrFail($advertisement);
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getIndexPaginate(Request $request): LengthAwarePaginator
    {
        return self::query()->select($this->selected_columns)
            ->with($this->with_relationships)
            ->scopes($this->with_scopes)
            ->when($request->filled('places'), function ($q) use ($request) {
                $q->whereIn('place', $request->places);
            })->paginate($request->get('perPage'));
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

    #region Relations

    /**
     * @return MorphOne
     */
    public function image(): MorphOne
    {
        return $this->singleMedia()->select('id', 'model_id', 'model_type', 'disk', 'files')
            ->where('collection', self::MEDIA_COLLECTION_ADVERTISEMENTS);
    }

    #endregion

    #region Scopes

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeActive(Builder $builder)
    {
        $builder->where('status',AdvertisementStatus::Active);
    }

    #endregion
}
