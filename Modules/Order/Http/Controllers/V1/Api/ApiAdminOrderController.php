<?php

namespace Modules\Order\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use BenSampo\Enum\Rules\EnumKey;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Order\Entities\Order;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Transformers\Api\Admin\ApiOrderResource;
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
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('order', ApiOrderResource::make(Order::init()->findOrFailById($order, ['address', 'products', 'products.image'])))
            ->send();
    }

    /**
     * @OA\Post(
     *     path="/admin/orders/{id}/change-status",
     *     summary="تغییر وضعیت سفارش",
     *     description="تغییر وضعیت سفارش",
     *     tags={"سفارشات - پنل مدیریت"},
     *     @OA\Parameter(
     *         description="شناسه سفارش",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="number"),
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"_method","status"},
     *                 @OA\Property(
     *                     property="_method",
     *                     type="string",
     *                     default="put",
     *                     enum={"put"},
     *                     description="این مقدار باید بصورت ثابت شود",
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="string",
     *                     enum={"Pending","Success","Canceled","Fail"}
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="عملیات موفق",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function changeStatus(Request $request, $order)
    {
        $order = Order::init()->findOrFailById($order);
        ApiResponse::init($request->all(), [
            'status' => ['required', new EnumKey(OrderStatus::class)]
        ])->validate();
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('order', ApiOrderResource::make($order->changeStatus(OrderStatus::getValue($request->status))))
            ->send();
    }
}
