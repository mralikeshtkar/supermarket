<?php

namespace Modules\Media\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Modules\Media\Database\factories\MediaFactory;

class Media extends Model
{
    use HasFactory;

    #region Constance

    protected $fillable = [
        'user_id',
        'model_id',
        'model_type',
        'base_url',
        'disk',
        'files',
        'collection',
        'extension',
        'priority',
    ];

    protected $casts = [
        'files' => 'array',
    ];

    #endregion

    #region Methods

    protected static function boot()
    {
        parent::boot();
        static::deleted(function (Media $media) {
            Storage::disk($media->disk)->delete(json_decode($media->getRawOriginal('files'),true));
        });
    }

    /**
     * @return MediaFactory
     */
    protected static function newFactory(): MediaFactory
    {
        return MediaFactory::new();
    }

    /**
     * @return Media
     */
    public static function init(): Media
    {
        return new self();
    }

    public function findByIdOrFail($media)
    {
        return self::query()->findOrFail($media);
    }

    #endregion

    #region Mutators

    /**
     * @param $value
     * @return array|null
     */
    public function getFilesAttribute($value): ?array
    {
        return $value ? array_map(function ($item) {
            return Storage::disk($this->disk)->url($item);
        }, json_decode($value, true)) : null;
    }

    #endregion

    #region Relationships

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }

    #endregion

    #region Scopes

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeOrderByAscPriority(Builder $builder)
    {
        $builder->orderBy('priority');
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeOrderByDescPriority(Builder $builder)
    {
        $builder->orderByDesc('priority');
    }

    #endregion
}
