<?php

use Modules\Rack\Rules\ProductExistsInRackRowRule;

return [
    ProductExistsInRackRowRule::class => ":attribute معتبر نمیباشد.",
    'attributes' => [
        'name' => "نام محصول",
        'slug' => "لینک محصول",
        'price' => "قیمت محصول",
        'image' => "تصویر محصول",
        'categories_id' => "دسته‌های محصول",
        'tags_id' => "تگ‌های محصول",
    ],
];
