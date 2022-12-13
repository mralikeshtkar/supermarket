<?php

use Modules\Discount\Rules\DiscountableIdRule;
use Modules\Discount\Rules\DiscountableTypeRule;
use Modules\Discount\Rules\DiscountCodeRule;

return [
    DiscountCodeRule::class => ":attribute معتبر نمیباشد.",
    DiscountableTypeRule::class => ":attribute معتبر نمیباشد.",
    DiscountableIdRule::class => ":attribute معتبر نمیباشد.",
    'attributes' => [
        'code' => "کد تخفیف",
        'amount' => "میزان تخفیف",
        'start_at' => "تاریخ شروع تخفیف",
        'expire_at' => "تاریخ پایان تخفیف",
        'usage_limitation' => "محدودیت افراد",
        'uses' => "discount uses",
        'description' => "توضیحات تخفیف",
        'discountables' => "قابل تخفیف",
        'is_percent' => "تخفیف برحسب درصد یا قیمت",
        'discountables.discountable_type'=>"نوع قابل تخفیف",
        'discountables.discountable_ids'=>"شناسه قابل تخفیف",
        'priority'=>"اولویت تخفیف",
    ],
];
