<?php

use Modules\Rack\Enums\RackRowStatus;
use Modules\Rack\Enums\RackStatus;

return [
    'enum' => ':attribute انتخاب شده معتبر نمیباشد.',
    RackStatus::class => [
        RackStatus::Pending => "در حال بررسی",
        RackStatus::Accepted => "تایید شده",
        RackStatus::Rejected => "رد شده",
    ],
    RackRowStatus::class => [
        RackRowStatus::Active => "فعال",
        RackRowStatus::Inactive => "غیرفعال",
    ],
];
