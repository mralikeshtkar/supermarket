<?php

namespace Modules\News\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;

class NewsStatus extends \BenSampo\Enum\Enum implements LocalizedEnum
{
    const Pending = "pending";
    const Accepted = "accepted";
    const Rejected = "rejected";

    public static function getLocalizationKey(): string
    {
        return 'news::enums.' . static::class;
    }
}
