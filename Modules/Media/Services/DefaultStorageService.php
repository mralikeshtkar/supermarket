<?php

namespace Modules\Media\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class DefaultStorageService extends BaseStorageService implements Interfaces\FileStorageInterface
{
    /**
     * @param $disk
     * @param $file
     * @param array $options
     * @return array
     */
    public static function store($disk, $file, array $options = []): array
    {
        /** @var UploadedFile $file */
        $directory = Arr::get($options, 'directory', '');
        $urls['original'] = parent::upload($disk, $file, $directory);
        return $urls;
    }

}
