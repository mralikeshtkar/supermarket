<?php

namespace Modules\Discount\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class DiscountStatus extends Enum implements LocalizedEnum
{
    const Pending = 0;
    const Accepted = 1;
    const Rejected = 2;

    public static function getLocalizationKey(): string
    {
        return 'discount::enums.' . static::class;
    }

    public function getCssClass()
    {
        return trans('discount::enums.statuses.' . static::class . '.' . $this->value);
    }
}
