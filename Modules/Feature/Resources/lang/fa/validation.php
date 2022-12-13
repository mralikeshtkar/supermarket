<?php

use Modules\Feature\Rules\AttributableRule;
use Modules\Feature\Rules\FeatureableRule;

return [
    FeatureableRule::class => ":attribute معتبر نمیباشند.",
    AttributableRule::class => ":attribute معتبر نمیباشند.",
    'attributes' => [
        'featureable' => 'قابل ویژگی',
        'attributable' => 'دارای ویژگی',
        'title' => 'عنوان',
        'parent_id' => 'ویژگی والد',
        'has_option' => 'دارای گزینه',
        'is_filter' => 'آیتم فیلتر',
        'option_value' => "مقدار گزینه",
        'option_id' => "گزینه ویژگی",
        'attribute_value' => "عنوان صفت",
    ],
];
