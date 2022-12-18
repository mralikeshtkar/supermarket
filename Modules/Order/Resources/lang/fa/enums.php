<?php

use Modules\Order\Enums\OrderInvoiceStatus;
use Modules\Order\Enums\OrderStatus;

return [
    OrderStatus::class => [
        OrderStatus::AwaitingReview => "در انتطار بررسی",
        OrderStatus::Preparation => "آماده سازی",
        OrderStatus::DeliverToDispatcher => "تحویل به پیک ارسال",
        OrderStatus::DeliveryToCustomer => "تحویل به مشتری",
    ],
    OrderInvoiceStatus::class => [
        OrderInvoiceStatus::Pending => "در حال پرداخت",
        OrderInvoiceStatus::Success => "موفق",
        OrderInvoiceStatus::Canceled => "لغو شده",
        OrderInvoiceStatus::Fail => "ناموفق",
    ],
];
