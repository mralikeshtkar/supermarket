<?php

use Modules\Category\Rules\CategoryRule;
use Modules\Storeroom\Rules\ProductExistsInEntranceRule;
use Modules\Storeroom\Rules\StoreroomOutProductRule;

return [
    CategoryRule::class => ":attribute aren't valid",
    StoreroomOutProductRule::class => ":attribute is incorrect",
    ProductExistsInEntranceRule::class => ":attribute is incorrect",
    'attributes' => [
        'name' => "category name",
        'slug' => "category slug",
        'parent_id' => "category parent id",
        'status' => "category status",
        'image' => "category image",
    ],
];
