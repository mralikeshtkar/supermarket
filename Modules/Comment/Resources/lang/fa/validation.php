<?php

use Modules\Comment\Rules\CommentableRule;
use Modules\Comment\Rules\CommentAcceptedRule;

return [
    CommentableRule::class => ":attribute معتبر نمیباشند.",
    CommentAcceptedRule::class => ":attribute تایید نشده است.",
    'attributes' => [
        'title' => "عنوان دیدگاه",
        'body' => "متن دیدگاه",
        'advantage' => "نقاط مثبت دیدگاه",
        'disadvantage' => "نقاط منفی دیدگاه",
        'parent_id' => "دیدگاه والد",
        'commentable' => "قابل اظهار نظر",
    ],
];
