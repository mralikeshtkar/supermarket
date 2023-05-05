<?php

namespace Modules\User\Exports\Admin;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\User\Entities\User;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class UsersExport implements FromCollection,WithMapping,WithColumnFormatting,ShouldAutoSize
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
            "ali".substr($row->mobile,3)
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => '#############'
        ];
    }
}
