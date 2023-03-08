<?php

namespace Modules\Dashboard\Http\Controllers\V1\Api\Admin;

use Carbon\CarbonPeriod;
use Illuminate\Routing\Controller;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Order\Entities\Invoice;

class ApiAdminDashboardController extends Controller
{

    public function index()
    {
        $totalSalesLast30Days = Invoice::init()->getTotalSaleInPeriod(30);
        $totalSalesLast30Days =collect(CarbonPeriod::between(today()->subDays(30),today())->toArray())->mapWithKeys(function ($item) use ($totalSalesLast30Days){
            $i = $totalSalesLast30Days->firstWhere('created_at',$item);
            return [
                verta($item)->formatJalaliDate()=> $i ? $i->amount_sum : 0
            ];
        });
        return ApiResponse::message(trans('Received information successfully'))
            ->addData('totalSalesLast30DaysDates', $totalSalesLast30Days->keys())
            ->addData('totalSalesLast30DaysValues', $totalSalesLast30Days->values())
            ->send();
    }

}
