<?php

namespace Modules\Media\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Modules\Media\Services\Interfaces\FileStorageInterface;

class FileStorageService
{
    private string $disk;
    private $file;
    private array $options;
    private FileStorageInterface $storageService;

    /**
     * @param string $disk
     * @param $file
     * @param array $options
     */
    public function __construct(string $disk,$file, array $options = [])
    {
        $this->disk = $disk;
        $this->file = $file;
        $this->options = $options;
        $this->storageService = $this->getHandlerStorageService();
    }

    public function store()
    {
        return $this->storageService->store($this->disk, $this->file, $this->options);
    }

    private function getHandler()
    {
        if ($handler = collect(config('media.handlers'))->firstWhere(function ($handler, $key) {
            return in_array($this->file->getClientOriginalExtension(), Arr::get($handler, 'extensions'));
        })) return $handler;
        else return config('media.default');
    }

    private function getHandlerStorageService(): mixed
    {
        return resolve(Arr::get($this->getHandler(), 'handler'));
    }
}
