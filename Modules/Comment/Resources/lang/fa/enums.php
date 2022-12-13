<?php

use Modules\Comment\Enums\CommentStatus;

return [
    CommentStatus::class => [
        CommentStatus::Pending => "در حال بررسی",
        CommentStatus::Accepted => "تایید شده",
        CommentStatus::Rejected => "رد شده",
    ],
];
