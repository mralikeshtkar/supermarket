<?php

use Modules\Core\Rules\MobileRule;
use Modules\Core\Rules\PhoneNumberRule;
use Modules\Core\Rules\ProductModelRule;

return [
    MobileRule::class => ":attribute isn't valid format.",
    PhoneNumberRule::class => ":attribute isn't valid format.",
    ProductModelRule::class => ":attribute is incorrect.",
];
