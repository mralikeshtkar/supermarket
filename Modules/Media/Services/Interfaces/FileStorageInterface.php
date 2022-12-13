<?php

namespace Modules\Media\Services\Interfaces;

interface FileStorageInterface
{
    public static function upload($disk, $file, $directory);
}
