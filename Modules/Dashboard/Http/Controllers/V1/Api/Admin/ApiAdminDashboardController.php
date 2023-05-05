<?php

namespace Modules\Dashboard\Http\Controllers\V1\Api\Admin;

use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Order\Entities\Invoice;
use Modules\Order\Entities\Order;
use Modules\Order\Enums\OrderStatus;

class ApiAdminDashboardController extends Controller
{

    public function index()
    {
        return ApiResponse::message(trans('Received information successfully'))
            ->addData('totalSalesLast30DaysDates', [])
            ->addData('totalSalesLast30DaysValues', [])
            ->send();
        $totalSalesLast30Days = Invoice::init()->getTotalSaleInPeriod(30);
        $totalSalesLast30Days = collect(CarbonPeriod::between(today()->subDays(30), today())->toArray())->mapWithKeys(function ($item) use ($totalSalesLast30Days) {
            $i = $totalSalesLast30Days->firstWhere('created_at', $item);
            return [
                verta($item)->formatJalaliDate() => $i ? $i->amount_sum : 0
            ];
        });
        return ApiResponse::message(trans('Received information successfully'))
            ->addData('totalSalesLast30DaysDates', $totalSalesLast30Days->keys())
            ->addData('totalSalesLast30DaysValues', $totalSalesLast30Days->values())
            ->send();
    }

    public function notifications(Request $request)
    {
        $orders_count = Order::query()
            ->where('status', OrderStatus::AwaitingReview)
            ->whereHas('invoices', function ($q) {
                $q->success();
            })->count();
        $notifications = collect([])->when(!$orders_count, function (Collection $collection) use ($orders_count) {
            $collection->push([
                'text' => "{$orders_count} سفارش در بخش سفارشات ثبت شده است که بررسی نشده اند",
                'link' => "/orders",
            ])->push([
                'text' => "{$orders_count} سفارش در بخش سفارشات ثبت شده است که بررسی نشده اند",
                'link' => "/orders",
            ]);
        })->toArray();
        return ApiResponse::message(trans("Received information successfully"))
            ->addData('notifications', $notifications)
            ->send();
    }

}
