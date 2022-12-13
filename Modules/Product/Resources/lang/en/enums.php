<?php

use Modules\Product\Enums\ProductStatus;
use Modules\Product\Enums\ProductUnitStatus;

return [
    ProductStatus::class => [
        ProductStatus::Pending => "Pending",
        ProductStatus::Accepted => "Accepted",
        ProductStatus::Rejected => "Rejected",
    ],
    ProductUnitStatus::class => [
        ProductUnitStatus::Pending => "Pending",
        ProductUnitStatus::Accepted => "Accepted",
        ProductUnitStatus::Rejected => "Rejected",
    ],
    'statuses'=>[
        ProductStatus::class => [
            ProductStatus::Pending => "badge-warning",
            ProductStatus::Accepted => "badge-success",
            ProductStatus::Rejected => "badge-danger",
        ],
        ProductUnitStatus::class => [
            ProductUnitStatus::Pending => "badge-warning",
            ProductUnitStatus::Accepted => "badge-success",
            ProductUnitStatus::Rejected => "badge-danger",
        ],
    ],
];
