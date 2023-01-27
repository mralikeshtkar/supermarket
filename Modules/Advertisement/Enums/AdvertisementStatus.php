<?php

namespace Modules\Advertisement\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;

class AdvertisementStatus extends \BenSampo\Enum\Enum implements LocalizedEnum
{
    const Active = "active";
    const Inactive = "inactive";

    public static function getLocalizationKey(): string
    {
        return 'advertisement::enums.' . static::class;
    }
}
