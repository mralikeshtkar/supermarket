<?php

namespace Modules\User\Exports\Admin;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Modules\User\Entities\User;

class UsersExport implements FromCollection
{
    public function collection()
    {
        return User::query()
            ->limit(10)
            ->get();
    }
}
