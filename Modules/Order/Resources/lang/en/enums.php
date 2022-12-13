<?php

use Modules\Order\Enums\OrderStatus;

return [
    OrderStatus::class => [
        OrderStatus::Pending => "Pending",
        OrderStatus::Success => "Success",
        OrderStatus::Canceled => "Canceled",
        OrderStatus::Fail => "Fail",
    ],
    'statuses' => [
        OrderStatus::class => [
            OrderStatus::Pending => "badge-warning",
            OrderStatus::Success => "badge-success",
            OrderStatus::Canceled => "badge-danger",
            OrderStatus::Fail => "badge-danger",
        ],
    ],
];
