<?php

namespace Modules\Order\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use BenSampo\Enum\Rules\EnumKey;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Order\Entities\Order;
use Modules\Order\Enums\OrderInvoiceStatus;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Transformers\Api\Admin\ApiAdminOrderResource;
use OpenApi\Annotations as OA;

class ApiAdminOrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/orders",
     *     summary="لیست سفارشات بصورت صفحه بندی",
     *     description="ثبت سفارش",
     *     tags={"سفارشات - پنل مدیریت"},
     *     @OA\Parameter(
     *         description="شماره سفارش",
     *         in="query",
     *         name="order",
     *         required=false,
     *         @OA\Schema(type="number"),
     *     ),
     *     @OA\Parameter(
     *         description="نام سفارش دهنده",
     *         in="query",
     *         name="user_name",
     *         required=false,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         description="شناسه سفارش دهنده",
     *         in="query",
     *         name="user_id",
     *         required=false,
     *         @OA\Schema(type="number"),
     *     ),
     *     @OA\Parameter(
     *         description=" 1401/8/24 - از تاریخ",
     *         in="query",
     *         name="from",
     *         required=false,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         description=" 1401/8/24 - تا تاریخ",
     *         in="query",
     *         name="to",
     *         required=false,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         description="وضعیت",
     *         in="query",
     *         name="status",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"Pending","Success","Canceled","Fail"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="عملیات موفق",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function index(Request $request)
    {
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('orders', Order::init()->getAdminIndexPaginate($request))
            ->send();
    }

    /**
     * @OA\Get(
     *     path="/admin/orders/{id}",
     *     summary="نمایش جزئیات سفارش",
     *     description="نمایش جزئیات سفارش",
     *     tags={"سفارشات - پنل مدیریت"},
     *     @OA\Parameter(
     *         description="شناسه سفارش",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="number"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="عملیات موفق",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function show($order)
    {
        $order = Order::init()->selectColumns([
            'id',
            'user_id',
            'total',
            'discount_amount',
            'amount',
            'shipping_cost',
            'total_cart',
            'discount',
            'status',
            'created_at',
        ])->withRelationships([
            'address',
            'address.city:id,province_id,name',
            'address.city.province:id,name',
            'products',
            'products.image',
        ])->findOrFailById($order);
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('order', ApiAdminOrderResource::make($order))
            ->addData('statuses', OrderStatus::asSelectArray())
            ->send();
    }

    /**
     * @param Request $request
     * @param $order
     * @return JsonResponse
     */
    public function changeStatus(Request $request, $order)
    {
        $order = Order::init()->selectColumns(['id'])->findOrFailById($order);
        ApiResponse::init($request->all(),[
            'status'=>['required',new EnumValue(OrderStatus::class)]
        ])->validate();
        $order->changeStatus($request->status);
        return ApiResponse::message(trans('The operation was done successfully'))->send();
    }

}
