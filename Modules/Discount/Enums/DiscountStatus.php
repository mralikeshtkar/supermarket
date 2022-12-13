<?php

namespace Modules\Discount\Enums;

use BenSampo\Enum\Enum;

class DiscountStatus extends Enum
{
    const Pending = 0;
    const Accepted = 1;
    const Rejected = 2;
}
