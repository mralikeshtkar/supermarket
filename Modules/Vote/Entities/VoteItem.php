<?php

namespace Modules\Vote\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\User;
use Modules\Vote\Database\factories\VoteItemFactory;

class VoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'vote_id',
        'title',
    ];

    private array $selected_columns = ['*'];

    private array $with_relationships = [];

    private array $with_scopes = [];

    #region Methods

    /**
     * @return VoteItem
     */
    public static function init(): VoteItem
    {
        return new self();
    }

    /**
     * @return VoteItemFactory
     */
    protected static function newFactory(): VoteItemFactory
    {
        return VoteItemFactory::new();
    }

    /**
     * @param $users_count
     * @param $item_users_count
     * @return float
     */
    public function getPercentOfTotal($users_count, $item_users_count): float
    {
        return $item_users_count ? round(($users_count / $item_users_count) * 100,1) : 0;
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
     * @return Builder|Model
     */
    public function store(Request $request): Model|Builder
    {
        return self::query()->create([
            'vote_id' => $request->vote_id,
            'title' => $request->title,
        ]);
    }

    /**
     * @param $user_id
     * @return void
     */
    public function attachUser($user_id)
    {
        $this->users()->attach($user_id);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function updateRow(Request $request): bool
    {
        return $this->update([
            'vote_id' => $request->vote_id,
            'title' => $request->title,
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
    public function vote(): BelongsTo
    {
        return $this->belongsTo(Vote::class);
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'vote_item_user')
            ->withTimestamps();
    }

    #endregion

}
