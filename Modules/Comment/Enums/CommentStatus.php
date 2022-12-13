<?php

namespace Modules\Comment\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class CommentStatus extends Enum implements LocalizedEnum
{
    const Pending = 0;
    const Accepted = 1;
    const Rejected = 2;

    public static function getLocalizationKey(): string
    {
        return 'comment::enums.' . static::class;
    }

    public function getCssClass()
    {
        return trans('comment::enums.statuses.' . static::class . '.' . $this->value);
    }
}
