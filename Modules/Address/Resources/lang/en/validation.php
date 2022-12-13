<?php

use Modules\Address\Entities\Address;
use Modules\Address\Entities\City;
use Modules\Address\Entities\Province;
use Modules\Address\Rules\PostalCodeRule;

return [
    PostalCodeRule::class=>":attribute is incorrect.",
    'attributes' => [
        Province::class => [
            'name' => "province name",
        ],
        City::class => [
            'name' => "city name",
            'province_id' => "parent province",
        ],
        Address::class => [
            'province_id' => "province",
            'city_id' => "city",
            'mobile' => "recipient mobile",
            'name' => "recipient name",
            'address' => "address",
            'postal_code' => "postal code",
        ],
    ],
];
