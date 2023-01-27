<?php

use Modules\News\Enums\NewsCategoryStatus;
use Modules\News\Enums\NewsCommentStatus;
use Modules\News\Enums\NewsStatus;

return [
    NewsCommentStatus::class=>[
        NewsCommentStatus::Accepted=>"accepted",
        NewsCommentStatus::Pending=>"pending",
        NewsCommentStatus::Rejected=>"rejected",
    ],
    NewsStatus::class=>[
        NewsStatus::Accepted=>"accepted",
        NewsStatus::Pending=>"pending",
        NewsStatus::Rejected=>"rejected",
    ],
    NewsCategoryStatus::class=>[
        NewsCategoryStatus::Accepted=>"accepted",
        NewsCategoryStatus::Pending=>"pending",
        NewsCategoryStatus::Rejected=>"rejected",
    ],
];
