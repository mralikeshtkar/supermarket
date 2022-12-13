<?php

use Modules\Brand\Enums\BrandStatus;

return [
    BrandStatus::class => [
        BrandStatus::Pending => "در حال بررسی",
        BrandStatus::Accepted => "تایید شده",
        BrandStatus::Rejected => "رد شده",
    ],
];
