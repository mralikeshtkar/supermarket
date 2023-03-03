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
        OrderStatus::class => [
            OrderStatus::AwaitingReview => "badge-success",
            OrderStatus::Preparation => "badge-success",
            OrderStatus::DeliverToDispatcher => "badge-success",
            OrderStatus::DeliveryToCustomer => "badge-success",
        ],
        OrderInvoiceStatus::class => [
            OrderInvoiceStatus::Pending => "badge-warning",
            OrderInvoiceStatus::Success => "badge-success",
            OrderInvoiceStatus::Canceled => "badge-danger",
            OrderInvoiceStatus::Fail => "badge-danger",
        ],
    ],
];
