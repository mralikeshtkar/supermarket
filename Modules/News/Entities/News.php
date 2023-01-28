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
use Modules\News\Database\factories\NewsFactory;
use Modules\News\Enums\NewsStatus;
use Modules\User\Entities\User;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'body',
        'status',
    ];

    private array $selected_columns = ['*'];

    private array $with_relationships = [];

    private array $with_scopes = [];

    #region Methods

    /**
     * @return News
     */
    public static function init(): News
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
            'news_category_id' => $request->news_category_id,
            'title' => $request->title,
            'body' => $request->body,
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
            'news_category_id' => $request->news_category_id,
            'title' => $request->title,
            'body' => $request->body,
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
     * @return NewsFactory
     */
    protected static function newFactory(): NewsFactory
    {
        return NewsFactory::new();
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

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function paginateAdmin(Request $request): LengthAwarePaginator
    {
        return self::query()->select($this->selected_columns)
            ->with($this->with_relationships)
            ->scopes($this->with_scopes)
            ->latest()
            ->paginate($request->get('perPage', 10));
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
            ->latest()
            ->paginate($request->get('perPage', 10));
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

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getPaginateComments(Request $request): LengthAwarePaginator
    {
        return $this->comments()->select($this->selected_columns)
            ->with($this->with_relationships)
            ->scopes($this->with_scopes)
            ->paginate($request->get('perPage', 10));
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
    public function newsCategory(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'category_id');
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(NewsComment::class);
    }

    #endregion

    #region Scopes

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeAccepted(Builder $builder)
    {
        $builder->where('status', NewsStatus::Accepted);
    }

    #endregion
}
