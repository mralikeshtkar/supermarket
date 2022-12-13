<?php

namespace Modules\Brand\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class BrandStatus extends Enum implements LocalizedEnum
{
    const Pending = 0;
    const Accepted = 1;
    const Rejected = 2;

    public static function getLocalizationKey(): string
    {
        return 'brand::enums.' . static::class;
    }

    public function getCssClass()
    {
        return trans('brand::enums.classes.' . static::class.'.'.$this->value);
    }
}
