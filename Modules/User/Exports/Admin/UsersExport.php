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
            ->select(['id','name','mobile'])
            ->limit(10)
            ->get();
    }
}
