<?php

use Modules\Category\Rules\CategoryRule;
use Modules\Storeroom\Rules\ProductExistsInEntranceRule;
use Modules\Storeroom\Rules\StoreroomOutProductRule;

return [
    CategoryRule::class => ":attribute معتبر نمیباشند.",
    StoreroomOutProductRule::class=>":attribute معتبر نمیباشند.",
    ProductExistsInEntranceRule::class=>":attribute معتبر نمیباشند.",
    'attributes' => [
        'name' => "نام دسته",
        'slug' => "لینک دسته",
        'parent_id' => "دسته والد",
        'status' => "وضعیت دسته",
        'image' => "تصویر دسته",
    ],
];
