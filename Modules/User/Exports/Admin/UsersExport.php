<?php

namespace Modules\User\Exports\Admin;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\User\Entities\User;

class UsersExport implements FromQuery, WithMapping, WithColumnFormatting, WithHeadings, ShouldAutoSize
{
    use Exportable;

    private $request = null;

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
            ->select(['id', 'name', 'mobile', 'created_at'])
            ->when($this->request, function ($q) {
                $q->filter($this->request);
            });
    }

    /**
     * @param $request
     * @return $this
     */
    public function withFilter($request): static
    {
        $this->request = $request;
        return $this;
    }

}
