<?php

namespace Modules\Order\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Address\Entities\Address;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Order\Entities\Order;
use Modules\Order\Transformers\Api\Admin\ApiAdminOrderResource;
use OpenApi\Annotations as OA;

class ApiOrderController extends Controller
{
    /**
     * @OA\Post(
     *     path="/orders",
     *     summary="ثبت سفارش",
     *     description="ثبت سفارش",
     *     tags={"سفارشات"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"address_id"},
     *                 @OA\Property(
     *                     property="address_id",
     *                     description="شناسه آدرس",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="discount",
     *                     description="کد تخفیف",
     *                     type="string",
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
    public function store(Request $request)
    {
        ApiResponse::init($request->all(), [
            'address_id' => ['required', 'exists:' . Address::class . ',id'],
            'discount' => ['nullable', 'string'],
        ])->validate();
        try {
            return DB::transaction(function () use ($request) {
                return Order::init()->store($request);
            });
        } catch (\Throwable $e) {
            return $e;
        }
    }
}
