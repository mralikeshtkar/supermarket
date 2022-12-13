<?php

namespace Modules\Category\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class CategoryStatus extends Enum implements LocalizedEnum
{
    const Pending = 0;
    const Accepted = 1;
    const Rejected = 2;

    public static function getLocalizationKey(): string
    {
        return 'category::enums.' . static::class;
    }
}
