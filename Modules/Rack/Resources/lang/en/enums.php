<?php

use Modules\Rack\Enums\RackRowStatus;
use Modules\Rack\Enums\RackStatus;

return [
    RackStatus::class => [
        RackStatus::Pending => "Pending",
        RackStatus::Accepted => "Accepted",
        RackStatus::Rejected => "Rejected",
    ],
    RackRowStatus::class => [
        RackRowStatus::Active => "Active",
        RackRowStatus::Inactive => "Inactive",
    ],
    'statuses' => [
        RackStatus::class => [
            RackStatus::Pending => "badge-warning",
            RackStatus::Accepted => "badge-success",
            RackStatus::Rejected => "badge-danger",
        ],
        RackRowStatus::class => [
            RackRowStatus::Active => "badge-success",
            RackRowStatus::Inactive => "badge-danger",
        ],
    ],
];
