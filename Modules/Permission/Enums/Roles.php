<?php

namespace Modules\Permission\Enums;

use BenSampo\Enum\Enum;

class Roles extends Enum
{
    const SUPER_ADMIN = [
        'name_en'=>"super admin",
        'name_fa'=>"مدیرکل",
        'permissions'=>[],
    ];
}
