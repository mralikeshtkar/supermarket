<?php

use Modules\Brand\Enums\BrandStatus;

return [
    BrandStatus::class => [
        BrandStatus::Pending => "Pending",
        BrandStatus::Accepted => "Accepted",
        BrandStatus::Rejected => "Rejected",
    ],
    'classes' => [
        BrandStatus::class => [
            BrandStatus::Pending => "badge-warning",
            BrandStatus::Accepted => "badge-success",
            BrandStatus::Rejected => "badge-danger",
        ],
    ],
];
