<?php

use Modules\Category\Enums\CategoryStatus;

return [
    CategoryStatus::class => [
        CategoryStatus::Pending => "Pending",
        CategoryStatus::Accepted => "Accepted",
        CategoryStatus::Rejected => "Rejected",
    ],
    'statuses' => [
        CategoryStatus::class => [
            CategoryStatus::Pending => "badge-warning",
            CategoryStatus::Accepted => "badge-success",
            CategoryStatus::Rejected => "badge-danger",
        ],
    ],
];
