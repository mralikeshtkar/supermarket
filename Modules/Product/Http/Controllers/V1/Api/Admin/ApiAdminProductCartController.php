<?php

namespace Modules\Product\Http\Controllers\V1\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Product\Entities\Product;
use Modules\Product\Transformers\V1\Api\Admin\AdminProductResource;
use Modules\User\Entities\User;

class ApiAdminProductCartController extends Controller
{
    /**
     * @param Request $request
     * @param $user
     * @return JsonResponse
     */
    public function products(Request $request, $user)
    {
        $user = User::init()->selectColumns(['id', 'cart'])->findOrFailById($user);
        $products = Product::init()->getCartProductExceptIds($request,array_keys(Arr::get($user, 'cart', [])));
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('products', ApiPaginationResource::make($products)->additional(['itemsResource' => AdminProductResource::class]))
            ->send();
    }

    /**
     * @param Request $request
     * @param $product
     * @param $user
     * @return mixed
     */
    public function update(Request $request, $product, $user)
    {
        $user = User::init()->selectColumns(['id'])->findOrFailById($user);
        $product = Product::init()->selectColumns(['id', 'name', 'price'])
            ->withScopes(['stock'])
            ->findOrFailById($product);
        ApiResponse::init($request->all(), [
            'quantity' => ['required', 'numeric', 'min:1', 'max:' . $product->quantity],
        ])->validate();
        return $user->updateCartProduct($request, $product);
    }

    /**
     * @param Request $request
     * @param $product
     * @param $user
     * @return mixed
     */
    public function destroy(Request $request, $product, $user)
    {
        $user = User::init()->selectColumns(['id'])->findOrFailById($user);
        $product = Product::init()->selectColumns(['id'])->findOrFailById($product);
        return $user->destroyCartProduct($request, $product);
    }
}
