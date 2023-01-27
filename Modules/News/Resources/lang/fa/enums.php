<?php

use Modules\News\Enums\NewsCategoryStatus;
use Modules\News\Enums\NewsCommentStatus;
use Modules\News\Enums\NewsStatus;

return [
    NewsCommentStatus::class => [
        NewsCommentStatus::Accepted => "تایید شده",
        NewsCommentStatus::Pending => "در حال بررسی",
        NewsCommentStatus::Rejected => "رد شده",
    ],
    NewsStatus::class => [
        NewsStatus::Accepted => "تایید شده",
        NewsStatus::Pending => "در حال بررسی",
        NewsStatus::Rejected => "رد شده",
    ],
    NewsCategoryStatus::class => [
        NewsCategoryStatus::Accepted => "تایید شده",
        NewsCategoryStatus::Pending => "در حال بررسی",
        NewsCategoryStatus::Rejected => "رد شده",
    ],
];
