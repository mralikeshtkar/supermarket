<?php

namespace Modules\Storeroom\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Storeroom\Entities\Storeroom;
use Modules\Storeroom\Entities\StoreroomEntrance;
use Modules\Storeroom\Rules\ProductExistsInEntranceRule;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAdminStoreroomEntranceController extends Controller
{
    /**
     * @param Request $request
     * @param $storeroom
     * @return JsonResponse
     */
    public function index(Request $request, $storeroom)
    {
        ApiResponse::authorize($request->user()->can('manage', Storeroom::class));
        try {
            $storeroom = Storeroom::init()->findByIdOrFail($storeroom);
            return ApiResponse::message(trans('storeroom::messages.received_information_successfully'))
                ->addData('entrances', $storeroom->getAdminEntrancePaginate($request))
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('storeroom::messages.storeroom_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('storeroom::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $storeroom
     * @return JsonResponse
     */
    public function store(Request $request, $storeroom)
    {
        ApiResponse::authorize($request->user()->can('manage', Storeroom::class));
        ApiResponse::init($request->all(), [
            'products' => ['required', 'array', 'min:1'],
            'products.*.id' => ['required', 'distinct', 'exists:products,id'],
            'products.*.quantity' => ['nullable', 'numeric', 'min:1'],
            'products.*.price' => ['required', 'numeric', 'min:1'],
        ], [], [
            'products' => trans('Products'),
            'products.*.id' => trans('Product id'),
            'products.*.quantity' => trans('Product quantity'),
            'products.*.price' => trans('Product price'),
        ])->validate();
        try {
            $storeroom = Storeroom::init()->findByIdOrFail($storeroom);
            StoreroomEntrance::init()->store($storeroom, $request);
            return ApiResponse::message(trans('storeroom::messages.storeroom_entrance_was_created'))->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('storeroom::messages.storeroom_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('storeroom::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $entrance
     * @return JsonResponse
     */
    public function products(Request $request, $entrance)
    {
        ApiResponse::authorize($request->user()->can('manage', Storeroom::class));
        try {
            $entrance = StoreroomEntrance::init()->findByIdOrFail($entrance);
            return ApiResponse::message(trans('storeroom::messages.received_information_successfully'))
                ->addData('products', $entrance->paginateProducts($request))
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('storeroom::messages.storeroom_entrance_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('storeroom::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $entrance
     * @return JsonResponse|mixed
     */
    public function updateProduct(Request $request, $entrance)
    {
        ApiResponse::authorize($request->user()->can('manage', Storeroom::class));
        ApiResponse::init($request->all(), [
            'products' => ['required', 'array', 'min:1'],
            'products.*.id' => ['required', 'exists:products,id', new ProductExistsInEntranceRule($entrance)],
            'products.*.quantity' => ['required', 'numeric', 'min:1'],
            'products.*.price' => ['required', 'numeric', 'min:1'],
        ])->validate();
        try {
            $entrance = StoreroomEntrance::init()->findByIdOrFail($entrance);
            return DB::transaction(function () use ($request, $entrance) {
                $entrance->updateProduct($request->products);
                return ApiResponse::message(trans('storeroom::messages.storeroom_entrance_products_have_been_updated'))->send();
            });
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('storeroom::messages.storeroom_entrance_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('storeroom::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    public function destroyProduct(Request $request, $entrance)
    {
        ApiResponse::authorize($request->user()->can('manage', Storeroom::class));
        try {
            $entrance = StoreroomEntrance::init()->findByIdOrFail($entrance);
            ApiResponse::init($request->all(), [
                'product_id' => ['required', new ProductExistsInEntranceRule($entrance->id)]
            ], [], [
                'product_id' => trans('Product id')
            ])->validate();
            $entrance->deleteProduct($request->product_id);
            return ApiResponse::message(trans('storeroom::messages.storeroom_entrance_product_have_been_deleted'))->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('storeroom::messages.storeroom_entrance_not_found'), Response::HTTP_NOT_FOUND);
        } catch (HttpResponseException $e) {
            return $e->getResponse();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('storeroom::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }
}
