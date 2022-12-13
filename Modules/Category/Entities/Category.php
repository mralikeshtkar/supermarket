<?php

namespace Modules\Category\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelIdea\Helper\Modules\Feature\Entities\_IH_Feature_C;
use Modules\Category\Database\factories\CategoryFactory;
use Modules\Category\Enums\CategoryStatus;
use Modules\Feature\Traits\HasFeature;
use Modules\Media\Entities\Media;
use Modules\Media\Traits\HasMedia;
use Modules\Product\Entities\Product;

class Category extends Model
{
    use HasFactory, HasFeature, HasMedia;

    #region Constants

    /**
     * Fill ables field.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
        'slug',
        'status',
    ];

    protected $with = [
        'parent',
    ];

    private array $selected_columns = ['*'];

    private array $with_relationships = [];

    private array $with_scopes = [];

    #endregion

    #region Methods

    /**
     * Check is exists categories with specified ids where status is accepted and categories count with these rules equals to ids count.
     *
     * @param array $ids
     * @return bool
     */
    public function whereExistsAcceptedWithIds(array $ids): bool
    {
        return self::query()->whereIn('id', $ids)->accepted()->count() == count($ids);
    }

    /**
     * Init category model factory
     *
     * @return CategoryFactory
     */
    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    /**
     * Initialize model.
     *
     * @return Category
     */
    public static function init(): Category
    {
        return new self();
    }

    public function allCategories()
    {
        return self::query()->get();
    }

    public function getAllCategories(Request $request, $category = null)
    {
        return self::query()
            ->without('parent')
            ->select(['id', 'parent_id', 'name'])
            ->with(['image'])
            ->accepted()
            ->when(is_null($category), function (Builder $builder) {
                $builder->parent();
            }, function (Builder $builder) use ($category) {
                $builder->where('parent_id', $category->id);
            })->paginate();
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function onlyAccepted(Request $request): Collection
    {
        return self::query()
            ->when($request->filled('search'), function (Builder $builder) use ($request) {
                $builder->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('slug', 'LIKE', '%' . $request->search . '%');
            })->accepted()
            ->get();
    }

    /**
     * @param Request $request
     * @param $category
     * @return LengthAwarePaginator
     */
    public function getAdminIndexPaginate(Request $request, $category = null): LengthAwarePaginator
    {
        return self::query()
            ->with('image')
            ->latest()
            ->when($request->filled('name'), function (Builder $builder) use ($request) {
                $builder->where('name', 'LIKE', '%' . $request->name . '%')
                    ->orWhere('slug', 'LIKE', '%' . $request->name . '%');
            })->when($category, function (Builder $builder) use ($category) {
                $builder->where('parent_id', $category->id);
            }, function (Builder $builder) use ($category) {
                $builder->whereNull('parent_id');
            })->paginate(2);
    }

    /**
     * Find or fail a category with slug.
     *
     * @param $category
     * @return Builder|Model
     */
    public function findOrFailById($category): Model|Builder
    {
        return self::query()->select($this->selected_columns)->with($this->with_relationships)
            ->scopes($this->with_scopes)->findOrFail($category);
    }

    /**
     * Store a category with request data.
     *
     * @param Request $request
     * @return Model|Builder
     */
    public function store(Request $request): Model|Builder
    {
        $category = self::query()->create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'slug' => Str::slug($request->slug),
            'parent_id' => $request->parent_id,
        ]);
        $category->when($request->hasFile('image'), function () use ($request, $category) {
            $category->setCollection(config('category.collection_gallery'))
                ->setDirectory('categories')
                ->addMedia($request->image);
        });
        return $category->load('images');
    }

    /**
     * Update a category with request data.
     *
     * @param $category
     * @param $request
     * @return mixed
     */
    public function updateCategory($category, $request): mixed
    {
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->slug),
            'parent_id' => $request->parent_id,
        ]);
        $category->when($request->hasFile('image'), function () use ($request, $category) {
            $category->setCollection(config('category.collection_gallery'))
                ->setDirectory('categories')
                ->addMedia($request->image);
        });
        return $category->refresh()->load('images');
    }

    /**
     * Change status category with specified status.
     *
     * @param $category
     * @param $status
     * @return mixed
     */
    public function changeStatus($category, $status): mixed
    {
        return $category->update([
            'status' => $status,
        ]);
    }

    /**
     * Destroy a category.
     *
     * @param $category
     * @return mixed
     */
    public function destroyCategory($category): mixed
    {
        return $category->delete();
    }

    /**
     * Get translated status.
     *
     * @return mixed
     */
    public function getStatus(): mixed
    {
        return CategoryStatus::fromValue($this->getAttribute('status'))->description;
    }

    /**
     * @return Collection|null
     */
    public function filters(): Collection|null
    {
        return $this->features()
            ->select(['id', 'title'])
            ->withWhereHas('children', function ($builder) {
                $builder->withWhereHas('options', function ($builder) {
                    $builder->select(['id', 'feature_id', 'value']);
                })->select(['id', 'parent_id', 'title', 'has_option', 'is_filter'])
                    ->where('has_option', true)
                    ->where('is_filter', true);
            })->parent()
            ->get();
    }

    #endregion

    #region Scopes

    /**
     * Add scope where status is accepted condition.
     *
     * @param Builder $builder
     * @return void
     */
    public function scopeAccepted(Builder $builder)
    {
        $builder->where('status', CategoryStatus::Accepted);
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

    #region Relationships

    /**
     * @return MorphToMany
     */
    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'categorizables');
    }

    /**
     * @return mixed
     */
    public function images(): mixed
    {
        return $this->media()
            ->select('id', 'model_id', 'model_type', 'disk', 'files')
            ->where('collection', config('category.collection_gallery'))
            ->orderByAscPriority()
            ->oldest();
    }

    /**
     * @return mixed
     */
    public function image(): mixed
    {
        return $this->morphOne(Media::class, 'model')
            ->select('id', 'model_id', 'model_type', 'disk', 'files')
            ->where('collection', config('category.collection_gallery'))
            ->orderByAscPriority()
            ->oldest();
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
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

    #endregion
}
