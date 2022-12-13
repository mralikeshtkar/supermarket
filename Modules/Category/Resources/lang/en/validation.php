<?php

use Modules\Category\Rules\CategoryRule;

return [
    CategoryRule::class => ":attribute aren't valid",
    'attributes' => [
        'name' => "Category name",
        'slug' => "Category slug",
        'parent_id' => "Category parent id",
        'status' => "Category status",
        'image' => "Category image",
    ],
];
