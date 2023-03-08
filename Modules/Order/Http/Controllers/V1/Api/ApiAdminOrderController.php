<?php

namespace Modules\Order\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use BenSampo\Enum\Rules\EnumValue;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Order\Entities\Order;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Transformers\Api\Admin\ApiAdminOrderResource;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
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
        ApiResponse::authorize($request->user()->can('manage', Order::class));
        $orders = Order::init()->getAdminIndexPaginate($request);
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('orders', ApiPaginationResource::make($orders)->additional(['itemsResource' => ApiAdminOrderResource::class]))
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
    public function show(Request $request, $order)
    {
        ApiResponse::authorize($request->user()->can('show', Order::class));
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
            'delivery_at',
        ])->withRelationships([
            'factor',
            'factor.city:id,province_id,name',
            'factor.city.province:id,name',
            'address',
            'address.city:id,province_id,name',
            'address.city.province:id,name',
            'products',
            'products.image',
        ])->findOrFailById($order);
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('order', ApiAdminOrderResource::make($order))
            ->addData('statuses', OrderStatus::asSelectArray())
            ->addData('min_delivery_date', Verta::now()->format('Y/n/j H:i'))
            ->send();
    }

    /**
     * @param Request $request
     * @param $order
     * @return JsonResponse
     */
    public function changeStatus(Request $request, $order)
    {
        ApiResponse::authorize($request->user()->can('changeStatus', Order::class));
        $order = Order::init()->selectColumns(['id'])->findOrFailById($order);
        ApiResponse::init($request->all(), [
            'status' => ['required', new EnumValue(OrderStatus::class)]
        ])->validate();
        $order->changeStatus($request->status);
        return ApiResponse::message(trans('The operation was done successfully'))->send();
    }

    /**
     * @param Request $request
     * @param $order
     * @return JsonResponse
     */
    public function deliveryDate(Request $request, $order)
    {
        ApiResponse::authorize($request->user()->can('deliveryDate', Order::class));
        $order = Order::init()->selectColumns(['id'])->findOrFailById($order);
        ApiResponse::init($request->all(), [
            'date' => ['nullable', 'jdatetime:Y/n/j H:i', 'jdatetime_after:' . Verta::now()->format('Y/n/j H:i') . ',Y/n/j H:i'],
        ])->validate();
        $request->merge(['date' => $request->filled('date') ? Verta::parseFormat('Y/n/j H:i', $request->date)->datetime() : null]);
        $order->updateDeliveryDate($request->date);
        $order = Order::init()->selectColumns(['id', 'delivery_at',])->findOrFailById($order->id);
        return ApiResponse::message(trans('The operation was done successfully'))
            ->addData('order', ApiAdminOrderResource::make($order))
            ->send();
    }

    public function factor(Request $request, $order=1)
    {
//        ApiResponse::authorize($request->user()->can('factor', Order::class));
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
            'delivery_at',
        ])->withRelationships([
            'factor',
            'factor.city:id,province_id,name',
            'factor.city.province:id,name',
            'address',
            'address.city:id,province_id,name',
            'address.city.province:id,name',
            'products',
        ])->findOrFailById($order);
        return response()->json(['pdf' => "test"]);
        $pdf = PDF::loadView('factor', ['order' => $order]);
    }

}
