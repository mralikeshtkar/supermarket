<?php

use Modules\Advertisement\Enums\AdvertisementPlace;
use Modules\Advertisement\Enums\AdvertisementStatus;

return [
    AdvertisementStatus::class=>[
        AdvertisementStatus::Active=>"active",
        AdvertisementStatus::Inactive=>"inactive",
    ],
    AdvertisementPlace::class=>[
        AdvertisementPlace::Top=>"top"
    ],
];
