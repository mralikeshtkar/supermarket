<?php

use Modules\Tag\Rules\TagRule;

return [
    TagRule::class => ":attribute aren't valid",
    'attributes' => [
        'name' => "tag name",
        'slug' => "tag slug",
    ],
];
