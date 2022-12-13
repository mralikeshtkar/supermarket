<?php

namespace Modules\Brand\Entities;

use Hekmatinasser\Verta\Verta;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Http\Request;
use LaravelIdea\Helper\Modules\Brand\Entities\_IH_Brand_C;
use LaravelIdea\Helper\Modules\Brand\Entities\_IH_Brand_QB;
use Modules\Brand\Database\factories\BrandFactory;
use Modules\Brand\Enums\BrandStatus;
use Modules\Media\Entities\Media;
use Modules\Media\Traits\HasMedia;
use Modules\Product\Entities\Product;

class Brand extends Model
{
    use HasFactory, HasMedia;

    #region Constance

    protected $fillable = [
        'name',
        'name_en',
        'slug',
        'status',
    ];

    protected $casts = [
        'image' => 'array',
    ];

    private array $selected_columns = ['*'];

    private array $with_relationships = [];

    private array $with_scopes = [];

    #endregion

    #region Methods

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($brand) {
            foreach ($brand->media()->get() as $media) {
                $media->delete();
            }
        });
    }

    /**
     * @return string
     */
    public function getTranslatedStatus(): string
    {
        return BrandStatus::getDescription($this->status);
    }

    /**
     * @return string
     */
    public function getStatusCssClass(): string
    {
        return BrandStatus::fromValue($this->status)->getCssClass();
    }

    /**
     * Init factory class.
     *
     * @return BrandFactory
     */
    protected static function newFactory(): BrandFactory
    {
        return BrandFactory::new();
    }

    /**
     * Initialize class.
     *
     * @return Brand
     */
    public static function init(): Brand
    {
        return new self();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function onlyAcceptedBrands(Request $request)
    {
        return self::query()
            ->when($request->filled('search'), function (Builder $builder) use ($request) {
                $builder->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('name_en', 'LIKE', '%' . $request->search . '%');
            })->accepted()
            ->get();
    }

    /**
     * Store a brand with media.
     *
     * @param Request $request
     * @return Builder|Model
     */
    public function store(Request $request): Model|Builder
    {
        /** @var Brand $brand */
        $brand = self::query()->create([
            'name' => $request->name,
            'name_en' => $request->name_en,
            'slug' => $request->slug,
        ]);
        $brand->when($request->hasFile('image'), function () use ($request, $brand) {
            $brand->removeAllMedia()
                ->setCollection(config('brand.collection_gallery'))
                ->setDirectory('brands')
                ->addMedia($request->image);
        });
        return $brand->refresh()->load('image');
    }

    /**
     * Find a brand with param column.
     *
     * @param $id
     * @param $column
     * @param $status
     * @return Model|Builder
     */
    public function findByColumnOrFail($id, $column = 'id', $status = null): Model|Builder
    {
        return self::query()
            ->where($column, $id)
            ->when($status, function (Builder $builder) use ($status) {
                $builder->where('status', $status);
            })->firstOrFail();
    }

    /**
     * Delete a brand from database.
     *
     * @return bool|null
     */
    public function destroyBrand(): ?bool
    {
        return $this->delete();
    }

    /**
     * @param Request $request
     * @return Brand
     */
    public function updateBrand(Request $request): Brand
    {
        $this->update([
            'name' => $request->name,
            'name_en' => $request->name_en,
            'slug' => $request->slug,
        ]);
        $this->when($request->hasFile('image'), function () use ($request) {
            $this->removeAllMedia()
                ->setCollection(config('brand.collection_gallery'))
                ->setDirectory('brands')
                ->addMedia($request->image);
        });
        return $this->refresh()->load('image');
    }

    /**
     * @param mixed $status
     * @return Brand
     */
    public function changeStatus(mixed $status): Brand
    {
        $this->update(['status' => $status]);
        return $this->refresh();
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAdminIndexPaginate(Request $request): LengthAwarePaginator
    {
        return self::query()
            ->with('image')
            ->when($request->filled('name'), function (Builder $builder) use ($request) {
                $builder->where('name', 'LIKE', "%" . $request->name . "%")
                    ->orWhere('name_en', 'LIKE', "%" . $request->name . "%");
            })->when($request->filled('date') && validateDate($request->date), function (Builder $builder) use ($request) {
                $builder->whereDate('created_at', Verta::parseFormat('Y/m/d', $request->date)->datetime());
            })->latest()
            ->paginate(3)
            ->appends($request->only(['name', 'date']));
    }

    /**
     * @param $brand
     * @return Model|Collection|array|Builder|Brand|_IH_Brand_C|_IH_Brand_QB|null
     */
    public function findOrFailById($brand): Model|Collection|array|Builder|Brand|_IH_Brand_C|_IH_Brand_QB|null
    {
        return self::query()
            ->with($this->with_relationships)
            ->select($this->selected_columns)
            ->scopes($this->with_scopes)
            ->findOrFail($brand);
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

    public function image(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')
            ->select('id', 'model_id', 'model_type', 'disk', 'files')
            ->where('collection', config('brand.collection_gallery'));
    }

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    #endregion

    #region Scopes

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeAccepted(Builder $builder)
    {
        $builder->where('status', BrandStatus::Accepted);
    }

    #endregion

}
