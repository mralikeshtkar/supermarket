<?php

namespace Modules\Media\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Modules\Media\Entities\Media;
use Modules\Media\Services\FileStorageService;
use Symfony\Component\HttpFoundation\Response;

trait HasMedia
{
    use HasRelationships;

    /**
     * @var string|null
     */
    public string|null $_base_url = null;

    /**
     * @var array
     */
    public array $_sizes = [];

    /**
     * @var string
     */
    public string $_disk = "public";

    /**
     * @var string|null
     */
    public string|null $_extension = null;

    /**
     * @var string|null
     */
    public string|null $_collection = null;

    /**
     * @var string|null
     */
    public string|null $_directory = null;

    /**
     * @var float|null
     */
    public float|null $_priority = null;

    /**
     * @var bool
     */
    public bool $_only_original = false;

    /**
     * @param string $base_url
     * @return $this
     */
    public function setBaseUrl(string $base_url): static
    {
        $this->_base_url = $base_url;
        return $this;
    }

    /**
     * @param array $sizes
     * @return $this
     */
    public function setSize(array $sizes): static
    {
        $this->_sizes = $sizes;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSize(): mixed
    {
        return $this->_sizes;
    }

    /**
     * @param string $disk
     * @return $this
     */
    public function setDisk(string $disk): static
    {
        $this->_disk = $disk;
        return $this;
    }

    /**
     * @return mixed|string
     */
    public function getDisk(): mixed
    {
        return $this->_disk;
    }

    /**
     * @param string $extension
     * @return $this
     */
    public function setExtension(string $extension): static
    {
        $this->_extension = $extension;
        return $this;
    }

    /**
     * @param string $collection
     * @return $this
     */
    public function setCollection(string $collection): static
    {
        $this->_collection = $collection;
        return $this;
    }

    /**
     * @return mixed|string|null
     */
    public function getCollection(): mixed
    {
        return $this->_collection;
    }

    /**
     * @param string $directory
     * @return $this
     */
    public function setDirectory(string $directory): static
    {
        $this->_directory = $directory;
        return $this;
    }

    /**
     * @return mixed|string|null
     */
    public function getDirectory(): mixed
    {
        return $this->_directory;
    }

    /**
     * @param float|string $priority
     * @return $this
     */
    public function setPriority(float|string $priority): static
    {
        $this->_priority = $priority;
        return $this;
    }

    /**
     * @return float|mixed|null
     */
    public function getPriority(): mixed
    {
        return $this->_priority;
    }

    /**
     * @return $this
     */
    public function onlyOriginal(): static
    {
        $this->_only_original = true;
        return $this;
    }

    /**
     * @param $file
     * @return Model|Media
     */
    public function addMedia($file): Model|Media
    {
        $urls = (new FileStorageService($this->getDisk(), $file, [
            'directory' => $this->getDirectory(),
            'sizes' => $this->getSize(),
        ]))->store();
        return $this->storeModel($urls, $file->getClientOriginalExtension());
    }

    /**
     * @param string|null $collection
     * @return $this
     */
    public function removeAllMedia(string $collection = null): static
    {
        $media = $this->media()->when($collection, function (Builder $builder) use ($collection) {
            $builder->where('collection', $collection);
        })->get();
        foreach ($media as $mediaItem) {
            $mediaItem->delete();
        }
        return $this;
    }

    /**
     * @return MorphMany
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    /**
     * @param Builder $builder
     * @param $value
     * @param $request
     * @return void
     */
    public function scopeFindByIdWithCollection(Builder $builder, $value, $request)
    {
        $builder->where('id', $value['id'])
            ->withWhereHas('media', function ($builder) use ($request) {
                $builder->where('id', $request->route('media'))
                    ->where('collection', $request->route('collection'));
            });
    }

    private function storeModel(array $urls, string $extension): Model|Media
    {
        return $this->media()->create([
            'user_id' => optional(auth()->user())->id,
            'base_url' => url('/'),
            'disk' => $this->getDisk(),
            'files' => $urls,
            'collection' => $this->getCollection(),
            'extension' => $extension,
            'priority' => 0,
        ]);
    }

    /**
     * @param array $media_ids
     * @return void
     */
    public function changeSortGallery(array $media_ids)
    {
        $iteration = 1;
        foreach ($media_ids as $media) {
            $this->gallery()->where('id', $media)->update(['priority' => $iteration]);
            $iteration++;
        }
    }

    /**
     * @param $media
     * @param $message
     * @return mixed
     */
    public function deleteMedia($media, $message = null): mixed
    {
        if ($media = $this->media()->find($media))
            return $media->delete();
        throw new ModelNotFoundException($message ?? trans("media::messages.media_not_found"), Response::HTTP_NOT_FOUND);
    }
}
