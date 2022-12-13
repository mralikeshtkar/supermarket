<?php

use Modules\Category\Enums\CategoryStatus;

return [
    CategoryStatus::class => [
        CategoryStatus::Pending => "Pending",
        CategoryStatus::Accepted => "Accepted",
        CategoryStatus::Rejected => "Rejected",
    ],
];
