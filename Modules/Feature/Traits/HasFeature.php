<?php

namespace Modules\Feature\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\Request;
use LaravelIdea\Helper\Modules\Feature\Entities\_IH_Feature_QB;
use Modules\Comment\Traits\HasComment;
use Modules\Feature\Entities\Feature;

trait HasFeature
{
    use HasRelationships;

    /**
     * @return MorphMany
     */
    public function features(): MorphMany
    {
        return $this->morphMany(Feature::class, 'featureable');
    }

    /**
     * Initialize class.
     *
     * @return $this
     */
    public static function init(): static
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
        return $this->features()
            ->create([
                'user_id' => optional($request->user())->id,
                'parent_id' => $request->parent_id,
                'title' => $request->title,
                'has_option' => boolval($request->has_option),
                'is_filter' => boolval($request->is_filter),
            ]);
    }
}
