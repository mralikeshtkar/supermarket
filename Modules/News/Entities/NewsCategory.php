<?php

namespace Modules\News\Entities;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Modules\News\Database\factories\NewsCategoryFactory;
use Modules\News\Enums\NewsCategoryStatus;
use Modules\User\Entities\User;

class NewsCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'title',
        'status',
    ];

    private array $selected_columns = ['*'];

    private array $with_relationships = [];

    private array $with_scopes = [];

    #region Methods

    /**
     * @return NewsCategory
     */
    public static function init(): NewsCategory
    {
        return new self();
    }

    /**
     * @return NewsCategoryFactory
     */
    protected static function newFactory(): NewsCategoryFactory
    {
        return NewsCategoryFactory::new();
    }

    /**
     * @param $newsCategory
     * @return array|Builder|Builder[]|Collection|Model|mixed|null
     */
    public function findOrFailById($newsCategory): mixed
    {
        return self::query()->select($this->selected_columns)
            ->with($this->with_relationships)
            ->scopes($this->with_scopes)
            ->findOrFail($newsCategory);
    }

    public function getPaginateNews(Request $request): LengthAwarePaginator
    {
        return $this->news()->select(['id', 'user_id', 'category_id', 'title', 'body', 'created_at'])
            ->with(['user:id,name,email'])
            ->latest()
            ->paginate($request->get('perPage', 10));
    }

    /**
     * @param Request $request
     * @return Model|Builder
     */
    public function store(Request $request): Model|Builder
    {
        return self::query()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $request->parent_id,
            'title' => $request->title,
            'status' => $request->status,
        ]);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function updateRow(Request $request): bool
    {
        return $this->update([
            'parent_id' => $request->parent_id,
            'title' => $request->title,
            'status' => $request->status,
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
     * @param Request $request
     * @param $newsCategory
     * @return LengthAwarePaginator
     */
    public function paginateAdmin(Request $request, $newsCategory = null): LengthAwarePaginator
    {
        return self::query()->select($this->selected_columns)
            ->with($this->with_relationships)
            ->scopes($this->with_scopes)
            ->when($newsCategory, function (Builder $builder) use ($newsCategory) {
                $builder->where('parent_id', $newsCategory);
            }, function ($builder) use ($newsCategory) {
                $builder->parent();
            })->latest()
            ->paginate($request->get('perPage', 10));
    }

    /**
     * @param Request $request
     * @param $newsCategory
     * @return Collection|array
     */
    public function getIndex(Request $request, $newsCategory = null): Collection|array
    {
        return self::query()->select($this->selected_columns)
            ->with($this->with_relationships)
            ->scopes($this->with_scopes)
            ->get();
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
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * @return HasMany
     */
    public function news(): HasMany
    {
        return $this->hasMany(News::class, 'category_id');
    }

    #endregion

    #region Scopes

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeParent(Builder $builder)
    {
        $builder->whereNull('parent_id');
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeAccepted(Builder $builder)
    {
        $builder->where('status', NewsCategoryStatus::Accepted);
    }

    #endregion
}
