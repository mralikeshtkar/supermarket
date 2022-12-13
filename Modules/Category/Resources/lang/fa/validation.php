<?php

use Modules\Category\Rules\CategoryRule;

return [
    CategoryRule::class => ":attribute معتبر نمیباشند.",
    'attributes' => [
        'name' => "نام دسته",
        'slug' => "لینک دسته",
        'parent_id' => "دسته والد",
        'status' => "وضعیت دسته",
        'image' => "تصویر دسته",
    ],
];
