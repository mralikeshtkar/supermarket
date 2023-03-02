<?php

namespace Modules\Brand\Http\Controllers\V1\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Brand\Entities\Brand;
use Modules\Brand\Enums\BrandStatus;
use Modules\Brand\Transformers\Api\ApiAdminBrandResource;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use function trans;

class ApiAdminBrandController extends Controller
{
    public function allBrands(Request $request)
    {
        return ApiResponse::message(trans('brand::messages.received_information_successfully'))
            ->addData('brands', Brand::init()->onlyAcceptedBrands($request))
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        ApiResponse::authorize($request->user()->can('manage', Brand::class));
        $brands=Brand::init()->getAdminIndexPaginate($request);
        return ApiResponse::message(trans('brand::messages.received_information_successfully'))
            ->addData('brands', ApiPaginationResource::make($brands)->additional(['itemsResource' => ApiAdminBrandResource::class]))
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::authorize($request->user()->can('create', Brand::class));
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
     * @param Request $request
     * @param $brand
     * @return JsonResponse
     */
    public function show(Request $request, $brand)
    {
        ApiResponse::authorize($request->user()->can('show', Brand::class));
        try {
            $brand = Brand::init()->findByColumnOrFail($brand, 'id');
            return ApiResponse::message(trans('brand::messages.received_information_successfully'))
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
     * @param Request $request
     * @param $brand
     * @return JsonResponse
     */
    public function update(Request $request, $brand)
    {
        ApiResponse::authorize($request->user()->can('edit', Brand::class));
        $request->merge([
            'slug' => Str::slug($request->slug),
            'name_en' => ucfirst($request->name_en),
        ]);
        ApiResponse::init($request->all(), [
            'name' => ['required', 'string'],
            'name_en' => ['required', 'string'],
            'slug' => ['required', 'string', Rule::unique(Brand::class, 'slug')->ignore($brand, 'id')],
            'image' => ['nullable', 'image'],
        ], [], trans('brand::validation.attributes'))->validate();
        try {
            $brand = Brand::init()->findByColumnOrFail($brand);
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
     * @param Request $request
     * @param $brand
     * @return JsonResponse
     */
    public function accept(Request $request, $brand)
    {
        ApiResponse::authorize($request->user()->can('changeStatus', Brand::class));
        return $this->_changeStatus($request, $brand, BrandStatus::Accepted());
    }

    /**
     * @param Request $request
     * @param $brand
     * @return JsonResponse
     */
    public function reject(Request $request, $brand)
    {
        ApiResponse::authorize($request->user()->can('changeStatus', Brand::class));
        return $this->_changeStatus($request, $brand, BrandStatus::Rejected());
    }

    /**
     * @param Request $request
     * @param $brand
     * @param mixed $status
     * @return JsonResponse
     */
    private function _changeStatus(Request $request, $brand, mixed $status): JsonResponse
    {
        try {
            $brand = Brand::init()->findByColumnOrFail($brand,);
            $brand = $brand->changeStatus($status->value);
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

    /**
     * @param Request $request
     * @param $brand
     * @return JsonResponse
     */
    public function destroy(Request $request, $brand)
    {
        ApiResponse::authorize($request->user()->can('destroy', Brand::class));
        try {
            $brand = Brand::init()->findByColumnOrFail($brand);
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
}
