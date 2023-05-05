<?php

namespace Modules\User\Exports\Admin;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\User\Entities\User;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class UsersExport implements FromCollection,WithMapping,WithColumnFormatting
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
            $row->mobile
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => DataType::TYPE_STRING
        ];
    }
}
