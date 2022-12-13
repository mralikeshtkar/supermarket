<?php

namespace Modules\Rack\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static Accepted()
 * @method static static Rejected()
 */
class RackStatus extends Enum implements LocalizedEnum
{
    const Pending = 0;
    const Accepted = 1;
    const Rejected = 2;

    public static function getLocalizationKey(): string
    {
        return 'rack::enums.' . static::class;
    }

    public function getCssClass()
    {
        return trans('rack::enums.statuses.' . static::class . '.' . $this->value);
    }
}
