<?php

namespace Modules\Product\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class FaqStatus extends Enum implements LocalizedEnum
{
    const Pending = ",pending";
    const Accepted = ",accepted";
    const Rejected = ",rejected";

    public static function getLocalizationKey(): string
    {
        return 'product::enums.' . static::class;
    }

}
