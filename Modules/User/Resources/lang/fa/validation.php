<?php

use Modules\User\Rules\FavouritableRule;
use Modules\User\Rules\UniqueMobileRule;

return [
    FavouritableRule::class => ":attribute معتبر نمیباشد.",
    UniqueMobileRule::class => ":attribute قبلا انتخاب شده است.",
    'attributes' => [
        'favouritable' => 'قابل علاقه‌مندی',
        'favouritable_id' => 'شناسه قابل علاقه‌مندی',
        'favouritable_type' => 'نوع قابل علاقه‌مندی',
    ],
];
