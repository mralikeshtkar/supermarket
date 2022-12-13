<?php

namespace Modules\Brand\Http\Controllers\V1\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Brand\Entities\Brand;
use Modules\Brand\Enums\BrandStatus;
use Modules\Brand\Transformers\V1\Api\BrandResource;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Product\Transformers\V1\Api\ProductResource;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiBrandController extends Controller
{
    /**
     * Store a brand from call api.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::authorize($request->user()->can('store', Brand::class));
        $request->merge([
            'slug' => Str::slug($request->slug),
            'name_en' => ucfirst($request->name_en),
        ]);
        ApiResponse::init($request->all(), [
            'name' => ['required', 'string'],
            'name_en' => ['required', 'string'],
            'slug' => ['required', 'string', 'unique:' . Brand::class . ',slug'],
            'image' => ['nullable', 'image'],
        ], [], trans('brand::validation.attributes'))->validate();
        try {
            $brand = Brand::init()->store($request);
            return ApiResponse::message(trans('brand::messages.brand_was_created'))
                ->addData('brand', $brand)
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('brand::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @OA\Get(
     *     path="/brands/{id}",
     *     summary="دریافت برند همراه با محصولات",
     *     description="",
     *     tags={"برند"},
     *     @OA\Parameter(
     *         description="شناسه برند",
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
    public function show(Request $request, $brand)
    {
        try {
            $brand = Brand::init()->selectColumns(['id', 'name', 'name_en'])
                ->withRelationships(['image'])
                ->withScopes(['accepted'])->findOrFailById($brand);
            $products = $brand->products()
                ->select(['id', 'name', 'price'])
                ->with(['image', 'model'])
                ->accepted()->paginate();
            return ApiResponse::message(trans('brand::messages.received_information_successfully'))
                ->addData('brand', new BrandResource($brand))
                ->addData('products', ApiPaginationResource::make($products)->additional(['itemsResource' => ProductResource::class]))
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('brand::messages.brand_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('brand::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Update a brand.
     *
     * @param Request $request
     * @param $slug
     * @return JsonResponse
     */
    public function update(Request $request, $slug)
    {
        ApiResponse::authorize($request->user()->can('update', Brand::class));
        $request->merge([
            'slug' => Str::slug($request->slug),
            'name_en' => ucfirst($request->name_en),
        ]);
        ApiResponse::init($request->all(), [
            'name' => ['required', 'string'],
            'name_en' => ['required', 'string'],
            'slug' => ['required', 'string', Rule::unique(Brand::class, 'slug')->ignore($slug, 'slug')],
            'image' => ['nullable', 'image'],
        ], [], trans('brand::validation.attributes'))->validate();
        try {
            $brand = Brand::init()->findByColumnOrFail($slug, 'slug');
            $brand = $brand->updateBrand($request);
            return ApiResponse::message(trans('brand::messages.brand_was_updated'))
                ->addData('brand', $brand)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('brand::messages.brand_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('brand::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Destroy a brand.
     *
     * @param Request $request
     * @param $slug
     * @return JsonResponse
     */
    public function destroy(Request $request, $slug)
    {
        ApiResponse::authorize($request->user()->can('destroy', Brand::class));
        try {
            $brand = Brand::init()->findByColumnOrFail($slug, 'slug');
            $brand->destroyBrand();
            return ApiResponse::message(trans('brand::messages.brand_was_deleted'))
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('brand::messages.brand_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('brand::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    public function accept(Request $request, $slug)
    {
        ApiResponse::authorize($request->user()->can('manage', Brand::class));
        return $this->_changeStatus($request, $slug, BrandStatus::Accepted());
    }

    public function reject(Request $request, $slug)
    {
        ApiResponse::authorize($request->user()->can('manage', Brand::class));
        return $this->_changeStatus($request, $slug, BrandStatus::Rejected());
    }

    /**
     * @param Request $request
     * @param $slug
     * @param mixed $status
     * @return JsonResponse
     */
    private function _changeStatus(Request $request, $slug, mixed $status): JsonResponse
    {
        try {
            $brand = Brand::init()->findByColumnOrFail($slug, 'slug');
            $brand->changeStatus($status->value);
            $brand->status = $status->description;
            return ApiResponse::message(trans('brand::messages.the_status_of_the_brand_has_been_successfully_changed'))
                ->addData('brand', $brand)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('brand::messages.brand_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('brand::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        }
    }
}
