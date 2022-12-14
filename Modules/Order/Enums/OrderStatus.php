<?php

namespace Modules\Order\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static static AwaitingReview()
 * @method static static Preparation()
 * @method static static DeliverToDispatcher()
 * @method static static DeliveryToCustomer()
 */
class OrderStatus extends Enum implements LocalizedEnum
{
    const AwaitingReview = 0;
    const Preparation = 1;
    const DeliverToDispatcher = 2;
    const DeliveryToCustomer = 3;

    public static function getLocalizationKey(): string
    {
        return 'order::enums.' . static::class;
    }

    public function getCssClass()
    {
        return trans('order::enums.statuses.' . static::class . '.' . $this->value);
    }
}
