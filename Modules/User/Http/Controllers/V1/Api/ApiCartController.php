<?php

namespace Modules\User\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Product\Entities\Product;
use OpenApi\Annotations as OA;

class ApiCartController extends Controller
{
    /**
     * @OA\Post(
     *     path="/users/cart",
     *     summary="افزودن محصول به سبد خرید",
     *     description="افزودن محصول به سبد خرید",
     *     tags={"سبد خرید"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"product_id","quantity"},
     *                 @OA\Property(
     *                     property="product_id",
     *                     type="number",
     *                     description="شناسه محصول"
     *                 ),
     *                 @OA\Property(
     *                     property="quantity",
     *                     type="number",
     *                     description="تعداد"
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
        $product = Product::init()
//            ->withScopes(['stock'])
            ->selectColumns(['id','quantity'])
            ->findOrFailById($request->product_id);
        ApiResponse::init($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'numeric', 'min:1', 'max:' . $product->quantity],
        ], [], [
            'product_id' => trans('Product'),
            'quantity' => trans('Quantity'),
        ])->validate();
        $request->merge(['quantity' => $request->filled('quantity') ? $request->quantity : 1]);
        return $request->user()->storeCart($request, $product);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function reduceQuantity(Request $request)
    {
        $product = Product::init()->selectColumns(['id','quantity'])
            /*->withScopes(['stock'])*/
            ->findOrFailById($request->product_id);
        ApiResponse::init($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'numeric', 'min:1', 'max:' . $product->quantity],
        ], [], [
            'product_id' => trans('Product'),
            'quantity' => trans('Quantity'),
        ])->validate();
        $request->merge(['quantity' => $request->filled('quantity') ? $request->quantity : 1]);
        return $request->user()->updateCart($request, $product);
    }

    /**
     * @OA\Get(
     *     path="/users/cart",
     *     summary="لیست سبد خرید",
     *     description="لیست سبد خرید",
     *     tags={"سبد خرید"},
     *     @OA\Response(
     *         response=200,
     *         description="عملیات موفق",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function index(Request $request)
    {
        $cart = $request->user()->getCart();
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('cart', $cart)
            ->send();
    }
}
