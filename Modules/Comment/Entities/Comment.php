<?php

namespace Modules\Comment\Entities;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use LaravelIdea\Helper\Modules\Comment\Entities\_IH_Comment_C;
use LaravelIdea\Helper\Modules\Comment\Entities\_IH_Comment_QB;
use Modules\Comment\Database\factories\CommentFactory;
use Modules\Comment\Enums\CommentStatus;
use Modules\Product\Entities\Product;
use Modules\User\Entities\User;

class Comment extends Model
{
    use HasFactory;

    #region Constance

    protected $fillable = [
        'user_id',
        'parent_id',
        'commentable_id',
        'commentable_type',
        'title',
        'body',
        'advantage',
        'disadvantage',
        'status',
        'rate',
    ];

    protected $casts = [
        'advantage' => 'array',
        'disadvantage' => 'array',
    ];

    #endregion

    #region Methods

    /**
     * Initialize class.
     *
     * @return Comment
     */
    public static function init(): Comment
    {
        return new self();
    }

    /**
     * @return string
     */
    public function getTranslatedStatus(): string
    {
        return CommentStatus::getDescription($this->status);
    }

    /**
     * @return string
     */
    public function getStatusClassName(): string
    {
        return CommentStatus::coerce($this->status)->getCssClass();
    }

    /**
     * Check exists accepted comment in table.
     *
     * @param mixed $id
     * @param string $column
     * @return mixed
     */
    public function existAcceptedComment(mixed $id, string $column = 'id'): mixed
    {
        return self::query()
            ->where($column, $id)
            ->accepted()
            ->exists();
    }

    /**
     * @return CommentFactory
     */
    protected static function newFactory(): CommentFactory
    {
        return CommentFactory::new();
    }

    /**
     * @param $comment
     * @return Model|Collection|array|_IH_Comment_QB|Builder|_IH_Comment_C|Comment|null
     */
    public function findByIdOrFail($comment): Model|Collection|array|_IH_Comment_QB|Builder|_IH_Comment_C|Comment|null
    {
        return self::query()->findOrFail($comment);
    }

    /**
     * @return Comment
     */
    public function updateStatus(): Comment
    {
        $this->update(['status' => CommentStatus::coerce($this->status)->is(CommentStatus::Accepted) ? CommentStatus::Rejected : CommentStatus::Accepted]);
        return $this->select(['id', 'commentable_id', 'commentable_type', 'status'])->findOrFail($this->id);
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|_IH_Comment_C|array
     */
    public function getAdminIndexPaginate(Request $request): LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|_IH_Comment_C|array
    {
        return self::query()
            ->select(['id', 'user_id', 'commentable_id', 'created_at', 'commentable_type', 'title', 'body', 'advantage', 'disadvantage', 'status', 'rate'])
            ->with(['user:id,name,email,mobile', 'commentable:id,name'])
            ->where(function (Builder $builder) use ($request) {
                $builder->when($request->filled('text'), function (Builder $builder) use ($request) {
                    $builder->where('title', 'LIKE', '%' . $request->text . '%')
                        ->orWhere('body', 'LIKE', '%' . $request->text . '%');
                })->when($request->filled('product'), function (Builder $builder) use ($request) {
                    $builder->whereHasMorph('commentable', Product::class, function ($builder) use ($request) {
                        $builder->where('name', 'LIKE', '%' . $request->product . '%');
                    });
                })->when($request->filled('mobile'), function (Builder $builder) use ($request) {
                    $builder->whereHas('user', function ($builder) use ($request) {
                        $builder->where('mobile', 'LIKE', '%' . $request->mobile . '%');
                    });
                });
            })->latest()
            ->paginate();
    }

    /**
     * @param $comment
     * @return Model|Collection|array|_IH_Comment_QB|Builder|_IH_Comment_C|Comment|null
     */
    public function findForShow($comment): Model|Collection|array|_IH_Comment_QB|Builder|_IH_Comment_C|Comment|null
    {
        return self::query()->with(['commentable:id,name'])
            ->findOrFail($comment);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function updateItem(Request $request): bool
    {
        return $this->update([
            'parent_id' => $request->parent_id,
            'title' => $request->title,
            'body' => $request->body,
            'rate' => $request->rate,
            'advantage' => $request->advantage,
            'disadvantage' => $request->disadvantage,
            'status' => $request->status,
        ]);
    }

    /**
     * @return bool|null
     */
    public function destroyItem(): ?bool
    {
        return $this->delete();
    }

    #endregion

    #region Relationships

    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #endregion

    #region Scopes

    public function scopeAccepted(Builder $builder)
    {
        $builder->where('status', CommentStatus::Accepted);
    }

    #endregion

}
