<?php

namespace Modules\User\Exports\Admin;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\User\Entities\User;

class UsersExport implements FromCollection,WithHeadings
{
    public function collection()
    {
        return User::query()
            ->select(['id','name','mobile'])
            ->limit(10)
            ->get();
    }

    public function headings(): array
    {
        return ['id','name','mobile'];
    }
}
