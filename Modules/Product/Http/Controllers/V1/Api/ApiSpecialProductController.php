<?php

namespace Modules\Product\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Product\Entities\Special;
use Modules\Product\Transformers\V1\Api\SpecialProductResource;
use OpenApi\Annotations as OA;

class ApiSpecialProductController extends Controller
{
    /**
     * @OA\Get (
     *     path="/special-products",
     *     summary="لیست محصولات ویژه - تنظیم تعداد ایتم ها در تنظیمات است",
     *     description="",
     *     tags={"محصولات ویژه"},
     *     @OA\Response(
     *         response=200,
     *         description="عملیات موفق",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function index(Request $request)
    {
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('specialProducts', SpecialProductResource::collection(Special::init()->getIndex($request)))
            ->send();
    }
}
