<?php

namespace Modules\Order\Exports\Admin;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\Order\Entities\Order;
use Modules\User\Entities\User;

class OrdersExport implements FromQuery, WithMapping, WithColumnFormatting, WithHeadings, ShouldAutoSize
{
    use Exportable;

    private $request = null;

    public function map($row): array
    {
        return [
            $row->id,
            number_format($row->total),
            number_format($row->discount),
            number_format($row->amount),
            verta($row->created_at)->formatJalaliDatetime(),
        ];
    }

    public function columnFormats(): array
    {
        return [

        ];
    }

    public function headings(): array
    {
        return [
            __('OrderId'),
            __('Total'),
            __('Discount'),
            __('Money Paid'),
            __('Date'),
        ];
    }

    public function query()
    {
        return Order::query()
            ->with(['address','invoices'])
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
