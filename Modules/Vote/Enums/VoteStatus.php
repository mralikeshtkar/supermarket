<?php

namespace Modules\Vote\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class VoteStatus extends Enum implements LocalizedEnum
{
    const Active = "active";
    const Inactive = "inactive";

    public static function getLocalizationKey(): string
    {
        return 'vote::enums.' . static::class;
    }
}
