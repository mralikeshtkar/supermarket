<?php

namespace Modules\Order\Enums;

class OrderAddressType extends \BenSampo\Enum\Enum
{
    const Normal = "normal";
    const Factor = "factor";

    public static function getLocalizationKey(): string
    {
        return 'order::enums.' . static::class;
    }
}
