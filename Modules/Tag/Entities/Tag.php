<?php

namespace Modules\Tag\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Product\Entities\Product;
use Modules\Tag\Database\factories\TagFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Tag extends Model
{
    use HasFactory,LogsActivity;

    #region Constance

    protected $fillable = [
        'user_id',
        'name',
        'slug',
    ];

    #endregion

    #region Methods

    /**
     * Initialize class.
     *
     * @return Tag
     */
    public static function init(): Tag
    {
        return new self();
    }

    public function allTags(Request $request)
    {
        return self::query()
            ->when($request->filled('search'), function (Builder $builder) use ($request) {
                $builder->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('slug', 'LIKE', '%' . $request->search . '%');
            })->get();
    }

    public function getAdminIndexPaginate(Request $request)
    {
        return self::query()
            ->when($request->filled('name'), function (Builder $builder) use ($request) {
                $builder->where('name', 'LIKE', '%' . $request->name . '%')
                    ->orWhere('slug', 'LIKE', '%' . $request->name . '%');
            })->latest()
            ->paginate()
            ->appends($request->only('name'));
    }

    /**
     * Init models factory.
     *
     * @return TagFactory
     */
    protected static function newFactory(): TagFactory
    {
        return TagFactory::new();
    }

    /**
     * Store a tag to database.
     *
     * @param Request $request
     * @return Model|Builder
     */
    public function store(Request $request): Model|Builder
    {
        return self::query()->create([
            'user_id' => optional($request->user())->id,
            'name' => $request->name,
            'slug' => $request->slug,
        ]);
    }

    public function findOrFailById($tag, array $relations = [])
    {
        return self::query()
            ->with($relations)
            ->findOrFail($tag);
    }

    /**
     * Update a tag and return updated tag.
     *
     * @param Model|Builder $tag
     * @param Request $request
     * @return Model
     */
    public function updateTag(Model|Builder $tag, Request $request): Model
    {
        $tag->update([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);
        return $tag->refresh();
    }

    /**
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * Destroy a tag from database.
     *
     * @param Model|Builder $tag
     * @return mixed
     */
    public function destroyTag(Model|Builder $tag): mixed
    {
        return $tag->delete();
    }

    /**
     * Get count tags with param ids.
     *
     * @param mixed $value
     * @return int
     */
    public function whereInIdsCount(mixed $value): int
    {
        return self::query()
            ->whereIn('id', $value)
            ->count();
    }

    #endregion

    #region Relationships

    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'taggable');
    }

    #endregion
}
