<?php

use Modules\Tag\Rules\TagRule;

return [
    TagRule::class => ":attribute معتبر نمیباشند.",
    'attributes' => [
        'name' => "نام تگ",
        'slug' => "لینک تگ",
    ],
];
