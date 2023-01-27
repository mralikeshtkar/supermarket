<?php

namespace Modules\Advertisement\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;

class AdvertisementPlace extends \BenSampo\Enum\Enum implements LocalizedEnum
{
    const Top = "top";

    public static function getLocalizationKey(): string
    {
        return 'advertisement::enums.' . static::class;
    }
}
