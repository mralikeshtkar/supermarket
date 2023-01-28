<?php

namespace Modules\Poster\Entities;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Http\Request;
use Modules\Poster\Enums\PosterStatus;

class Poster extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
    ];

    private array $selected_columns = ['*'];

    private array $with_relationships = [];

    private array $with_scopes = [];

    const MEDIA_COLLECTION_POSTERS = "posters";

    const MEDIA_DIRECTORY_POSTERS = "posters";

    #region Methods

    /**
     * @return Poster
     */
    public static function init(): Poster
    {
        return new self();
    }

    /**
     * @param Request $request
     * @return Model|Builder
     */
    public function store(Request $request): Model|Builder
    {
        $poster = self::query()->create([
            'user_id' => $request->user()->id,
            'place' => $request->place,
            'status' => $request->status,
        ]);
        $poster->setCollection(self::MEDIA_COLLECTION_POSTERS)
            ->setDirectory(self::MEDIA_DIRECTORY_POSTERS)
            ->addMedia($request->image);
        return $poster;
    }

    /**
     * @param Request $request
     * @return void
     */
    public function updateRow(Request $request)
    {
        $this->update([
            'status' => $request->status,
        ]);
        if ($request->hasFile('image')) {
            $this->removeAllMedia(self::MEDIA_COLLECTION_POSTERS)
                ->setCollection(self::MEDIA_COLLECTION_POSTERS)
                ->setDirectory(self::MEDIA_DIRECTORY_POSTERS)
                ->addMedia($request->image);
        }
    }

    public function destroyRow()
    {
        $this->removeAllMedia(self::MEDIA_COLLECTION_POSTERS);
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
     * @param $poster
     * @return array|Builder|Builder[]|Collection|Model|mixed|null
     */
    public function findOrFailById($poster): mixed
    {
        return self::query()
            ->with($this->with_relationships)
            ->select($this->selected_columns)
            ->scopes($this->with_scopes)
            ->findOrFail($poster);
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
            ->paginate($request->get('perPage'));
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
            ->where('collection', self::MEDIA_COLLECTION_POSTERS);
    }

    #endregion

    #region Scopes

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeActive(Builder $builder)
    {
        $builder->where('status', PosterStatus::Active);
    }

    #endregion
}
