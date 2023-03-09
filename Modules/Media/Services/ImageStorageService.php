<?php

namespace Modules\Media\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Intervention\Image\ImageManagerStatic as Image;

class ImageStorageService extends BaseStorageService implements Interfaces\FileStorageInterface
{
    public function store(string $disk, $file, array $options = []): array
    {
        return $this->prepareUrl($disk, $file, $options);
    }

    private function resizeImage($disk, $file, $directory, $size): bool|string
    {
        dd($file);
        /** @var UploadedFile $file */
        $image = Image::make($file);
        $img = $image->resize(
            $size['w'], $size['h'],
            function ($constraint) {
                $constraint->aspectRatio();
            }
        );
        parent::upload($disk, $img->stream()->detach(), $directory);
        return $directory;
    }

    /**
     * @param string $disk
     * @param $file
     * @param array $options
     * @return array
     */
    private function prepareUrl(string $disk, $file, array $options = []): array
    {
        $directory = Arr::get($options, 'directory', '');
        $urls['original'] = parent::upload($disk, $file, $directory);
        if (Arr::has($options, 'sizes')) {
            foreach ($this->getSizes($options) as $key => $size) {
                $path = $directory . '/' . $key . '/' . $file->hashName();
                $urls[$key] = $this->resizeImage($disk, $file, $path, $size);
            }
        }
        return $urls;
    }

    private function getSizes(array $options = [])
    {
        $sizes = Arr::get($options, 'sizes', []);
        return count($sizes) ? $sizes : config('media.handlers.image.sizes');
    }
}
