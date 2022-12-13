<?php

use Modules\Media\Services\ImageStorageService;
use Modules\Media\Services\DefaultStorageService;

return [
    'name' => 'Media',
    'handlers' => [
        'image' => [
            'handler' => ImageStorageService::class,
            'extensions' => [
                'jpg',
                'jpeg',
                'png',
            ],
            'sizes' => [
                600 => [
                    'w' => 600,
                    'h' => 600,
                ],
                400 => [
                    'w' => 400,
                    'h' => 400,
                ],
            ],
        ],
    ],
    'default' => [
        'handler' => DefaultStorageService::class,
    ]
];
