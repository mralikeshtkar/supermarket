<?php

use Modules\Discount\Rules\DiscountableIdRule;
use Modules\Discount\Rules\DiscountableTypeRule;
use Modules\Discount\Rules\DiscountCodeRule;

return [
    DiscountCodeRule::class => ":attribute is incorrect.",
    DiscountableTypeRule::class => ":attribute is incorrect.",
    DiscountableIdRule::class => ":attribute is incorrect.",
    'attributes' => [
        'code' => "discount code",
        'amount' => "discount amount",
        'start_at' => "discount start_at",
        'expire_at' => "discount expire_at",
        'usage_limitation' => "discount usage_limitation",
        'uses' => "discount uses",
        'description' => "discount description",
        'discountables' => "discountables",
        'is_percent' => "is percent",
        'discountables.discountable_type'=>"discountable type",
        'discountables.discountable_ids'=>"discountable ids",
        'priority'=>"discount priority",
    ],
];
