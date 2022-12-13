<?php

namespace Modules\Comment\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use LaravelIdea\Helper\Modules\Product\Entities\_IH_Product_C;
use LaravelIdea\Helper\Modules\Product\Entities\_IH_Product_QB;
use Modules\Comment\Entities\Comment;
use Modules\Comment\Enums\CommentStatus;
use Modules\Permission\Enums\Permissions;
use Modules\Product\Entities\Product;

trait HasComment
{
    use RefreshDatabase;

    /**
     * Comments polymorphic relationship.
     *
     * @return MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Initialize class.
     *
     * @return $this
     */
    public function init(): static
    {
        return new self();
    }

    /**
     * Find with specified field and value.
     * if it doesn't exist throw not found exception.
     *
     * @param $id
     * @param string $column
     * @return Model|Builder
     */
    public function findByColumnOrFail($id, string $column = 'id'): Model|Builder
    {
        return self::query()
            ->where($column, $id)
            ->firstOrFail();
    }

    /**
     * Store a comment.
     *
     * @param Request $request
     * @return Model
     */
    public function storeComment(Request $request): Model
    {
        return $this->comments()->create([
            'user_id' => optional($request->user())->id,
            'parent_id' => $request->parent_id,
            'title' => $request->title,
            'body' => $request->body,
            'rate' => $request->rate,
            'advantage' => $request->advantage,
            'disadvantage' => $request->disadvantage,
            'status' => $request->user() && $request->user()->can(Permissions::MANAGE_COMMENTS) ?
                CommentStatus::Accepted :
                CommentStatus::Pending,
        ]);
    }
}
