<?php

namespace Modules\Transportation\Entities;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class Transportation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
    ];

    private array $selected_columns = ['*'];

    private array $with_relationships = [];

    private array $with_scopes = [];

    #region Methods

    /**
     * @return Transportation
     */
    public static function init(): Transportation
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
            'user_id'=>$request->user()->id,
            'title'=>$request->title,
        ]);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function updateRow(Request $request): bool
    {
        return $this->update([
            'title'=>$request->title,
        ]);
    }

    /**
     * @return bool|null
     */
    public function destroyRow(): ?bool
    {
        return $this->delete();
    }

    /**
     * @param $transportation
     * @return array|Builder|Builder[]|Collection|Model|mixed|null
     */
    public function findOrFailById($transportation): mixed
    {
        return self::query()->select($this->selected_columns)
            ->with($this->with_relationships)
            ->scopes($this->with_scopes)
            ->findOrFail($transportation);
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
}
