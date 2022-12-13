<?php

use Modules\Order\Enums\OrderStatus;

return [
    OrderStatus::class => [
        OrderStatus::Pending => "در حال پرداخت",
        OrderStatus::Success => "موفق",
        OrderStatus::Canceled => "لغو شده",
        OrderStatus::Fail => "ناموفق",
    ],
];
