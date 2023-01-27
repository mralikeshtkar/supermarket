<?php

namespace Modules\Dashboard\Http\Controllers\V1\Api\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Order\Entities\Invoice;
use Modules\Order\Entities\Order;
use Modules\Order\Enums\OrderInvoiceStatus;

class ApiAdminDashboardController extends Controller
{

    public function index()
    {
        $totalOrderCountGroupByStatus = Invoice::init()->getTotalOrderCountGroupByStatus();
        return ApiResponse::message(trans('Received information successfully'))
            ->addData('totalOrderCountGroupByStatus', $this->convertOrderInvoiceStatus($totalOrderCountGroupByStatus))
            ->send();
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    private function convertOrderInvoiceStatus(Collection $collection): Collection
    {
        return collect(OrderInvoiceStatus::asArray())->map(function ($item, $key) use ($collection) {
            return [
                'title' => OrderInvoiceStatus::getDescription($item),
                'value' => $collection->has($key) ? $collection[$key] : 0,
            ];
        });
    }

}
