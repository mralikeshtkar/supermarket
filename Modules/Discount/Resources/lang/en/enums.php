<?php

use Modules\Comment\Enums\CommentStatus;
use Modules\Discount\Enums\DiscountStatus;

return [
    DiscountStatus::class => [
        DiscountStatus::Pending => "Pending",
        DiscountStatus::Accepted => "Accepted",
        DiscountStatus::Rejected => "Rejected",
    ],
    'statuses' => [
        DiscountStatus::class => [
            DiscountStatus::Pending => "badge-warning",
            DiscountStatus::Accepted => "badge-success",
            DiscountStatus::Rejected => "badge-danger",
        ],
    ],
];
