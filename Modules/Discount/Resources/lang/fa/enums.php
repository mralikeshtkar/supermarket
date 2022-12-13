<?php

use Modules\Discount\Enums\DiscountStatus;

return [
    DiscountStatus::class => [
        DiscountStatus::Pending => "در حال بررسی",
        DiscountStatus::Accepted => "تایید شده",
        DiscountStatus::Rejected => "رد شده",
    ],
];
