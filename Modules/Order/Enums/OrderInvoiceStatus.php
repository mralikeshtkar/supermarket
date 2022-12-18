<?php

namespace Modules\Order\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static static Pending()
 * @method static static Success()
 * @method static static Canceled()
 * @method static static Fail()
 */
class OrderInvoiceStatus extends Enum implements LocalizedEnum
{
    const Pending = 0;
    const Success = 1;
    const Canceled = 2;
    const Fail = 3;

    public static function getLocalizationKey(): string
    {
        return 'order::enums.' . static::class;
    }

    public function getCssClass()
    {
        return trans('order::enums.statuses.' . static::class . '.' . $this->value);
    }
}
