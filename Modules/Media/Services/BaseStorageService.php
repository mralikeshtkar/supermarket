<?php

namespace Modules\Media\Services;

use Illuminate\Http\FileHelpers;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class BaseStorageService
{
    use FileHelpers;

    /**
     * @param $disk
     * @param $file
     * @param $directory
     * @return bool|string
     */
    public static function upload($disk, $file, $directory): bool|string
    {
        if (is_resource($file))
            return Storage::disk($disk)->put($directory, $file);
        return Storage::disk($disk)->putFileAs($directory, $file, uniqid() . time() . "." . $file->getClientOriginalExtension());
    }
}
