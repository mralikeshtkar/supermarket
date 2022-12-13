<?php

namespace Modules\Product\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class ProductStatus extends Enum implements LocalizedEnum
{
    const Pending = 0;
    const Accepted = 1;
    const Rejected = 2;

    public static function getLocalizationKey(): string
    {
        return 'product::enums.' . static::class;
    }

    public function getCssClass()
    {
        return trans('product::enums.statuses.' . static::class.'.'.$this->value);
    }

}
