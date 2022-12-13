<?php

use Modules\Comment\Rules\CommentableRule;
use Modules\Comment\Rules\CommentAcceptedRule;

return [
    CommentableRule::class => ":attribute isn't valid",
    CommentAcceptedRule::class => ":attribute isn't accepted",
    'attributes' => [
        'title' => "comment title",
        'body' => "comment body",
        'advantage' => "comment advantage",
        'disadvantage' => "comment disadvantage",
        'parent_id' => "comment parent id",
        'commentable' => "commentable",
    ],
];
