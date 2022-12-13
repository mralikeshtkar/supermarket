<?php

namespace Modules\Rack\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class RackRowStatus extends Enum implements LocalizedEnum
{
    const Active = 0;
    const Inactive = 1;

    public static function getLocalizationKey(): string
    {
        return 'rack::enums.' . static::class;
    }

    public function getCssClass()
    {
        return trans('rack::enums.statuses.' . static::class . '.' . $this->value);
    }
}
