<?php

return [
    'name' => 'Discount',
    'discountables' => [
        \Modules\Product\Entities\Product::class,
        \Modules\Category\Entities\Category::class,
    ],
];
