<?php

use Modules\Comment\Enums\CommentStatus;

return [
    CommentStatus::class => [
        CommentStatus::Pending => "Pending",
        CommentStatus::Accepted => "Accepted",
        CommentStatus::Rejected => "Rejected",
    ],
    'statuses'=>[
        CommentStatus::class => [
            CommentStatus::Pending => "badge-warning",
            CommentStatus::Accepted => "badge-success",
            CommentStatus::Rejected => "badge-danger",
        ],
    ],
];
