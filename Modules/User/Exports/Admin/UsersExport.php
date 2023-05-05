<?php

namespace Modules\User\Exports\Admin;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\User\Entities\User;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class UsersExport implements FromQuery, WithMapping, WithColumnFormatting, WithHeadings, ShouldAutoSize
{
    use Exportable;

    public function map($row): array
    {
        return [
            $row->name,
            '0' . substr($row->mobile, 3),
            verta($row->created_at)->formatJalaliDate(),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => '0'
        ];
    }

    public function headings(): array
    {
        return [
            __('First name and Last name'),
            __('Mobile'),
            __('Register date'),
        ];
    }

    public function query()
    {
        return User::query()
            ->select(['id', 'name', 'mobile', 'created_at']);
    }

    /**
     * @param $request
     * @return $this
     */
    public function withFilter($request): static
    {
        $this->query()->filter($request);
        return $this;
    }

}
