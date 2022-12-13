<?php

use Modules\User\Rules\FavouritableRule;
use Modules\User\Rules\UniqueMobileRule;

return [
    FavouritableRule::class => ":attribute isn't correct.",
    UniqueMobileRule::class => "The :attribute has already been taken.",
    'attributes' => [
        'favouritable' => 'favouritable',
        'favouritable_id' => 'favouritable id',
        'favouritable_type' => 'favouritable type',
    ],
];
