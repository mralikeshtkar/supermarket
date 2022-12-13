<?php

use Modules\Feature\Rules\AttributableRule;
use Modules\Feature\Rules\FeatureableRule;

return [
    FeatureableRule::class => ":attribute isn't valid",
    AttributableRule::class => ":attribute isn't valid",
    'attributes' => [
        'featureable' => 'featureable',
        'attributable' => 'attributable',
        'title' => 'title',
        'parent_id' => 'parent id',
        'has_option' => 'has option',
        'is_filter' => 'is filter',
        'option_value' => "option value",
        'option_id' => "option id",
        'attribute_value' => "attribute value",
    ],
];
