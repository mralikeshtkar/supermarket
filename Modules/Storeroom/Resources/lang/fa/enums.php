<?php

use Modules\Category\Enums\CategoryStatus;

return [
    CategoryStatus::class => [
        CategoryStatus::Pending => "در حال بررسی",
        CategoryStatus::Accepted => "تایید شده",
        CategoryStatus::Rejected => "رد شده",
    ],
];
