<?php

namespace Modules\Poster\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class PosterStatus extends Enum implements LocalizedEnum
{
    const Active = "active";
    const Inactive = "inactive";

    public static function getLocalizationKey(): string
    {
        return 'poster::enums.' . static::class;
    }
}
