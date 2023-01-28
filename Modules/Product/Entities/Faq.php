<?php

namespace Modules\Product\Entities;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Modules\Product\Enums\FaqStatus;
use Modules\User\Entities\User;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'parent_id',
        'body',
        'status',
    ];

    private array $selected_columns = ['*'];

    private array $with_relationships = [];

    private array $with_scopes = [];

    #region Methods

    /**
     * @return Faq
     */
    public static function init(): Faq
    {
        return new self();
    }

    /**
     * @param Request $request
     * @param $product
     * @param $faq
     * @return LengthAwarePaginator
     */
    public function paginateAdmin(Request $request,$product, $faq): LengthAwarePaginator
    {
        return self::query()->select($this->selected_columns)
            ->with($this->with_relationships)
            ->scopes($this->with_scopes)
            ->where('product_id',$product->id)
            ->when($faq, function ($q) use ($faq) {
                $q->where('parent_id', $faq->id);
            }, function ($q) {
                $q->parent();
            })->paginate($request->get('perPage'));
    }

    /**
     * @param Request $request
     * @param $product
     * @return LengthAwarePaginator
     */
    public function getIndexPaginate(Request $request, $product): LengthAwarePaginator
    {
        return self::query()->select($this->selected_columns)
            ->with($this->with_relationships)
            ->scopes($this->with_scopes)
            ->where('product_id',$product->id)
            ->paginate($request->get('perPage'));
    }

    /**
     * @param Request $request
     * @param $product
     * @return Model|Builder
     */
    public function store(Request $request,$product): Model|Builder
    {
        return self::query()->create(collect([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'parent_id' => $request->parent_id,
            'body' => $request->body,
        ])->when($request->filled('status'), function (Collection $collection) use ($request) {
            $collection->put('status', $request->status);
        })->toArray());
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function updateRow(Request $request): bool
    {
        return $this->update([
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
     * @param $faq
     * @return array|Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|Model|mixed|null
     */
    public function findOrFailById($faq): mixed
    {
        return self::query()->select($this->selected_columns)->with($this->with_relationships)
            ->scopes($this->with_scopes)->findOrFail($faq);
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getRepliesPaginate(Request $request): LengthAwarePaginator
    {
        return $this->replies()->accepted()
            ->select($this->selected_columns)
            ->with($this->with_relationships)
            ->scopes($this->with_scopes)->paginate($request->get('perPge',$this->perPage));
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAdminRepliesPaginate(Request $request): LengthAwarePaginator
    {
        return $this->replies()
            ->select($this->selected_columns)
            ->with($this->with_relationships)
            ->scopes($this->with_scopes)->paginate($request->get('perPge',$this->perPage));
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
    public function replies(): HasMany
    {
        return $this->hasMany(self::class,'parent_id')
            ->latest();
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
        $builder->where('status',FaqStatus::Accepted);
    }

    #endregion

}
