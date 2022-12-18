<?php

namespace Modules\Discount\Http\Controllers\V1\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Discount\Entities\Discount;
use Modules\Discount\Exceptions\DiscountIsInvalidException;
use Modules\Discount\Rules\DiscountableIdRule;
use Modules\Discount\Rules\DiscountableTypeRule;
use Modules\Discount\Rules\DiscountCodeRule;
use Modules\Product\Entities\Product;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiDiscountController extends Controller
{
    /**
     * @OA\Post(
     *     path="/discounts/check",
     *     summary="بررسی کد تخفیف",
     *     description="",
     *     tags={"تخفیف"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"discount"},
     *                 @OA\Property(
     *                     property="discount",
     *                     type="number",
     *                     description="کد تخفیف"
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
     * @throws DiscountIsInvalidException
     */
    public function check(Request $request)
    {
        ApiResponse::init($request->all(), [
            'discount' => ['required', 'string'],
        ])->validate();
        $discount = Discount::init()->withRelationships(['products:id', 'categories:id'])
            ->findValidDiscountByCode($request->discount);
        $cart = $request->user()->getCart($discount);
        return ApiResponse::message(trans("discount::messages.received_information_successfully"))
            ->addData('cart', $cart)
            ->send();
    }
}
