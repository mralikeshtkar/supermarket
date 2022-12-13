<?php

use Modules\Rack\Rules\ProductExistsInRackRowRule;

return [
    ProductExistsInRackRowRule::class => ":attribute is invalid.",
    'attributes' => [
        'name' => "product name",
        'slug' => "product slug",
        'price' => "product price",
        'image' => "product image",
        'categories_id' => "product categories",
        'tags_id' => "product tags",
    ],
];
