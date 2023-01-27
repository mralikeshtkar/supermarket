<?php

namespace Modules\Vote\Entities;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\User;
use Modules\Vote\Database\factories\VoteFactory;
use Modules\Vote\Enums\VoteStatus;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Vote extends Model
{
    use HasFactory, HasRelationships;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
    ];

    private array $selected_columns = ['*'];

    private array $with_relationships = [];

    private array $with_scopes = [];

    #region Methods

    /**
     * @return Vote
     */
    public static function init(): Vote
    {
        return new self();
    }

    /**
     * @return VoteFactory
     */
    protected static function newFactory(): VoteFactory
    {
        return VoteFactory::new();
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
            ->paginate($request->get('perPage'));
    }

    /**
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        return self::query()->create([
            'vote_id' => $request->vote_id,
            'title' => $request->title,
        ]);
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(VoteItem::class);
    }

    /**
     * @return HasOne
     */
    public function item(): HasOne
    {
        return $this->hasOne(VoteItem::class);
    }

    /**
     * @return HasOne|Builder
     */
    public function selectedItem(): HasOne|Builder
    {
        return $this->item()->whereHas('users',function ($q){
            $q->where('users.id',auth()->id());
        });
    }

    /**
     * @return HasManyDeep
     */
    public function itemUsers(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->items(), (new VoteItem())->users());
    }

    /**
     * @return HasManyDeep
     */
    public function itemUser(): HasManyDeep
    {
        return $this->hasOneDeepFromRelations($this->items(), (new VoteItem())->users());
    }

    #endregion

    #region Scopes

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeActive(Builder $builder)
    {
        $builder->where('status', VoteStatus::Active);
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeInactive(Builder $builder)
    {
        $builder->where('status', VoteStatus::Inactive);
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeItemUsersCount(Builder $builder)
    {
        $builder->withCount('itemUsers');
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeSelectedItemId(Builder $builder)
    {
        $builder->with(['selectedItem'=>function($q){
            $q->select(['id','vote_id']);
        }]);
    }

    #endregion
}
