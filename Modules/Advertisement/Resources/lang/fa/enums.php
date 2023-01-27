<?php

use Modules\Advertisement\Enums\AdvertisementPlace;
use Modules\Advertisement\Enums\AdvertisementStatus;

return [
    AdvertisementStatus::class=>[
        AdvertisementStatus::Active=>"فعال",
        AdvertisementStatus::Inactive=>"غیرفعال",
    ],
    AdvertisementPlace::class=>[
        AdvertisementPlace::Top=>"بالا"
    ],
];
