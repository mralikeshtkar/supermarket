<?php

use Modules\Product\Enums\FaqStatus;
use Modules\Product\Enums\ProductStatus;
use Modules\Product\Enums\ProductUnitStatus;

return [
    ProductStatus::class => [
        ProductStatus::Pending => "در حال بررسی",
        ProductStatus::Accepted => "تایید شده",
        ProductStatus::Rejected => "رد شده",
    ],
    ProductUnitStatus::class => [
        ProductUnitStatus::Pending => "در حال بررسی",
        ProductUnitStatus::Accepted => "تایید شده",
        ProductUnitStatus::Rejected => "رد شده",
    ],
    FaqStatus::class => [
        FaqStatus::Pending => "در حال بررسی",
        FaqStatus::Accepted => "تایید شده",
        FaqStatus::Rejected => "رد شده",
    ],
];
