<?php

use Modules\Core\Rules\MobileRule;
use Modules\Core\Rules\PhoneNumberRule;
use Modules\Core\Rules\ProductModelRule;

return [
    MobileRule::class => "فرمت :attribute معتبر نمیباشد.",
    PhoneNumberRule::class => "فرمت :attribute معتبر نمیباشد.",
    ProductModelRule::class => ":attribute معتبر نمیباشد.",
];
