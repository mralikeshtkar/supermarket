<?php

namespace Modules\Product\Http\Controllers\V1\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\Special;
use Modules\Product\Transformers\V1\Api\Admin\AdminSpecialProductsResource;
use Throwable;

class ApiAdminSpecialProductController extends Controller
{
    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function addProduct(Request $request, $product)
    {
        $product = Product::init()->findOrFailById($product);
        Special::init()->addProduct($request, $product);
        return ApiResponse::message(trans("product_registration_as_a_special_product_was_successfully_completed"))
            ->addData('specialProducts', ApiPaginationResource::make(Special::init()->getAdminIndexPaginate($request))->additional(['itemsResource' => AdminSpecialProductsResource::class]))
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('specialProducts', ApiPaginationResource::make(Special::init()->getAdminIndexPaginate($request))->additional(['itemsResource' => AdminSpecialProductsResource::class]))
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse|mixed
     */
    public function changeSort(Request $request)
    {
        ApiResponse::init($request->all(), [
            'specials' => ['required', 'array'],
            'specials.*' => ['exists:' . Special::class . ',id'],
        ])->validate();
        try {
            return DB::transaction(function () use ($request) {
                Special::init()->chartSort($request->specials);
                return ApiResponse::message(trans('product::messages.received_information_successfully'))
                    ->addData('specialProducts', ApiPaginationResource::make(Special::init()->getAdminIndexPaginate($request))->additional(['itemsResource' => AdminSpecialProductsResource::class]))
                    ->send();
            });
        } catch (Throwable $e) {
            return ApiResponse::sendError(trans("Error in operation"));
        }
    }

    /**
     * @param Request $request
     * @param $special
     * @return JsonResponse
     */
    public function destroy(Request $request, $special)
    {
        $special = Special::init()->findOrFailById($special);
        $special->destroyItem();
        return ApiResponse::message(trans('product::messages.special_product_successfully_removed'))
            ->addData('specialProducts', ApiPaginationResource::make(Special::init()->getAdminIndexPaginate($request))->additional(['itemsResource' => AdminSpecialProductsResource::class]))
            ->send();
    }
}
