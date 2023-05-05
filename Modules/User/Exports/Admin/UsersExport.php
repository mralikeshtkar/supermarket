<?php

namespace Modules\User\Exports\Admin;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\User\Entities\User;

class UsersExport implements FromCollection,WithMapping
{
    public function collection()
    {
        return User::query()
            ->select(['id','name','mobile'])
            ->limit(10)
            ->get();
    }

    public function map($row): array
    {
        return [
            $row->name,
            strval($row->mobile)
        ];
    }
}
