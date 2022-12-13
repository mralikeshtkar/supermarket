<?php

use Modules\Address\Entities\Address;
use Modules\Address\Entities\City;
use Modules\Address\Entities\Province;
use Modules\Address\Rules\PostalCodeRule;

return [
    PostalCodeRule::class=>":attribute معتبر نمیباشد.",
    'attributes' => [
        Province::class => [
            'name' => "نام استان",
        ],
        City::class => [
            'name' => "نام شهر",
            'province_id' => "استان والد",
        ],
        Address::class => [
            'province_id' => "استان",
            'city_id' => "شهر",
            'mobile' => "شماره گیرنده",
            'name' => "نام گیرنده",
            'address' => "آدرس",
            'postal_code' => "کد پستی",
        ],
    ],
];
