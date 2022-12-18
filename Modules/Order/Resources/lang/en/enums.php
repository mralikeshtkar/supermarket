<?php

use Modules\Order\Enums\OrderInvoiceStatus;
use Modules\Order\Enums\OrderStatus;

return [
    OrderStatus::class => [
        OrderStatus::AwaitingReview => "Awaiting review",
        OrderStatus::Preparation => "Preparation",
        OrderStatus::DeliverToDispatcher => "DeliverToDispatcher",
        OrderStatus::DeliveryToCustomer => "DeliveryToCustomer",
    ],
    OrderInvoiceStatus::class => [
        OrderInvoiceStatus::Pending => "Pending",
        OrderInvoiceStatus::Success => "Success",
        OrderInvoiceStatus::Canceled => "Canceled",
        OrderInvoiceStatus::Fail => "Fail",
    ],
    'statuses' => [
        OrderInvoiceStatus::class => [
            OrderInvoiceStatus::Pending => "badge-warning",
            OrderInvoiceStatus::Success => "badge-success",
            OrderInvoiceStatus::Canceled => "badge-danger",
            OrderInvoiceStatus::Fail => "badge-danger",
        ],
    ],
];
