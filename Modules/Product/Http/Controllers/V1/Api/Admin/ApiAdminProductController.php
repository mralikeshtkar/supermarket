<?php

namespace Modules\Product\Http\Controllers\V1\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Brand\Entities\Brand;
use Modules\Category\Rules\CategoryRule;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Rules\ProductModelRule;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Media\Entities\Media;
use Modules\Permission\Entities\Role;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductUnit;
use Modules\Product\Enums\ProductStatus;
use Modules\Product\Enums\ProductUnitStatus;
use Modules\Product\Transformers\V1\Api\Admin\AdminProductResource;
use Modules\Tag\Rules\TagRule;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use function config;
use function trans;

class ApiAdminProductController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        ApiResponse::authorize($request->user()->can('manage', Product::class));
        $products = Product::init()->getAdminIndexPaginate($request);
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('products', ApiPaginationResource::make($products)->additional(['itemsResource' => AdminProductResource::class]))
            ->addData('maximum_price', Product::init()->getMaximumPrice())
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function stocks(Request $request)
    {
        $products = Product::init()->getAdminStocks($request);
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('products', ApiPaginationResource::make($products)->additional(['itemsResource' => AdminProductResource::class]))
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function allStocks(Request $request)
    {
        $products = Product::init()->allStocks($request);
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('products', AdminProductResource::collection($products))
            ->send();
    }

    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function show(Request $request, $product)
    {
        ApiResponse::authorize($request->user()->can('show', Product::class));
        $product = Product::init()->withRelationships(['categories', 'tags'])->findOrFailById($product);
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('product', $product)
            ->send();
    }

    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function gallery(Request $request, $product)
    {
        ApiResponse::authorize($request->user()->can('gallery', Product::class));
        $product = Product::init()->withRelationships(['gallery:id,model_id,model_type,disk,files,priority'])->findOrFailById($product);
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('product', $product)
            ->send();
    }

    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function uploadGallery(Request $request, $product)
    {
        ApiResponse::authorize($request->user()->can('gallery', Product::class));
        $product = Product::init()->findOrFailById($product);
        $product->uploadGallery($request);
        return ApiResponse::message(trans('product::messages.gallery_was_uploaded'))->send();
    }

    /**
     * @param Request $request
     * @param $product
     * @param $media
     * @return JsonResponse
     */
    public function destroyGallery(Request $request, $product, $media)
    {
        ApiResponse::authorize($request->user()->can('gallery', Product::class));
        $product = Product::init()->findOrFailByIdCustomException($product);
        $product->deleteMedia($media, trans('product::messages.gallery_not_found'));
        return ApiResponse::message(trans('product::messages.gallery_was_uploaded'))->send();
    }

    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function changeSortGallery(Request $request, $product)
    {
        $product = Product::init()->withRelationships(['gallery'])->findOrFailById($product);
        ApiResponse::init($request->all(), [
            'media_ids' => ['required', 'array'],
            'media_ids.*' => ['exists:' . Media::class . ',id'],
        ])->validate();
        try {
            return DB::transaction(function () use ($request, $product) {
                $product->changeSortGallery($request->get('media_ids'));
                return ApiResponse::message(trans('product::messages.received_information_successfully'))
                    ->addData('product', $product->load('gallery'))
                    ->send();
            });
        } catch (Throwable $e) {
            return ApiResponse::message(trans('product::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->send();
        }
    }

    public function store(Request $request)
    {
        dd("salam");
        //ApiResponse::authorize($request->user()->can('store', Product::class));
        //$request->merge(['slug' => Str::slug($request->get('slug'))]);
        ApiResponse::init($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique(Product::class, 'name'),
            ],
            'slug' => [
                'required',
                'string',
                Rule::unique(Product::class, 'slug'),
            ],
            'image' => [
                'required',
                'image',
            ],
            'model' => [
                'nullable',
                new ProductModelRule(['fbx', 'obj'])
            ],
            'price' => [
                'required',
                'numeric',
                'min:' . config('product.minimum_product_price'),
            ],
            'categories_id' => [
                'nullable',
                'array',
                new CategoryRule(Product::class)
            ],
            'tags_id' => [
                'nullable',
                'array',
                new TagRule(Product::class)
            ],
            'brand_id' => [
                'nullable',
                'exists:' . Brand::class . ',id'
            ],
            'unit_id' => [
                'required',
                Rule::exists(ProductUnit::class, 'id')->where('status', ProductUnitStatus::Accepted)
            ],
        ], [], trans('product::validation.attributes'))->validate();
        $product = Product::init()->store($request);
        return ApiResponse::message(trans('product::messages.product_was_created'))
            ->addData('product', $product->load(['gallery', 'model']))
            ->send();
    }

    /**
     * Update a product.
     *
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function update(Request $request, $product)
    {
        ApiResponse::authorize($request->user()->can('update', Product::class));
        $request->merge(['slug' => Str::slug($request->get('slug'))]);
        ApiResponse::init($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique(Product::class, 'name')->ignore($product),
            ],
            'slug' => [
                'required',
                'string',
                Rule::unique(Product::class, 'slug')->ignore($product),
            ],
            'price' => [
                'required',
                'numeric',
                'min:' . config('product.minimum_product_price'),
            ],
            'categories_id' => [
                'nullable',
                'array',
                /* new CategoryRule(Product::class)*/
            ],
            'tags_id' => [
                'nullable',
                'array',
                new TagRule(Product::class)
            ],
            'brand_id' => [
                'nullable',
                'exists:' . Brand::class . ',id'
            ],
            'unit_id' => [
                'required',
                Rule::exists(ProductUnit::class, 'id')->where('status', ProductUnitStatus::Accepted)
            ],
        ], [], trans('product::validation.attributes'))->validate();
        try {
            $product = Product::init()->findOrFailById($product);
            $product = Product::init()->updateProduct($product, $request);
            return ApiResponse::message(trans('product::messages.product_was_updated'))
                ->addData('product', $product)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::message(trans('product::messages.product_not_found'), Response::HTTP_NOT_FOUND)
                ->addError('message', $e->getMessage())
                ->hasError()
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('product::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function accept(Request $request,$product)
    {
        ApiResponse::authorize($request->user()->can('manage', Product::class));
        return $this->_changeStatus($product, ProductStatus::Accepted);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function searchAll(Request $request)
    {
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('products', Product::init()->searchAll($request))
            ->send();
    }

    public function onlyAccepted(Request $request)
    {
        //todo only permissions => special
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('products', ApiPaginationResource::make(Product::init()->onlyAccepted($request))->additional(['itemsResource' => AdminProductResource::class]))
            ->send();
    }

    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function reject(Request $request,$product)
    {
        ApiResponse::authorize($request->user()->can('manage', Product::class));
        return $this->_changeStatus($product, ProductStatus::Rejected);
    }

    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function model(Request $request, $product)
    {
        ApiResponse::authorize($request->user()->can('model', Product::class));
        $product = Product::init()->findOrFailById($product, ['model']);
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('product', $product)
            ->send();
    }

    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function uploadModel(Request $request, $product)
    {
        ApiResponse::authorize($request->user()->can('model', Product::class));
        $product = Product::init()->findOrFailById($product, ['model']);
        ApiResponse::init($request->all(), [
            'model' => ['required', new ProductModelRule(['fbx', 'obj'])],
        ])->validate();
        $product->uploadModel($request->model);
        return ApiResponse::message(trans('product::messages.model_was_uploaded'))->send();
    }

    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function destroyModel(Request $request, $product)
    {
        ApiResponse::authorize($request->user()->can('model', Product::class));
        $product = Product::init()->findOrFailById($product);
        $product->deleteModel();
        return ApiResponse::message(trans('product::messages.model_was_deleted'))->send();
    }

    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function destroy(Request $request, $product)
    {
        ApiResponse::authorize($request->user()->can('destroy', Product::class));
        try {
            $product = Product::init()->findOrFailById($product);
            $product->destroyProduct();
            return ApiResponse::message(trans('product::messages.product_was_deleted'))
                ->addData('product', $product)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::message(trans('product::messages.product_not_found'), Response::HTTP_NOT_FOUND)
                ->addError('message', $e->getMessage())
                ->hasError()
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('product::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param $product
     * @param $status
     * @return JsonResponse
     */
    private function _changeStatus($product, $status): JsonResponse
    {
        $product = Product::init()->findByColumnOrFail($product);
        try {
            $product = $product->changeStatus($status);
            return ApiResponse::message(trans('product::messages.product_status_was_updated'))
                ->addData('product', new AdminProductResource($product))
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('product::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }
}
