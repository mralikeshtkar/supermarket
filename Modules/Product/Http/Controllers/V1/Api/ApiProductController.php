<?php

namespace Modules\Product\Http\Controllers\V1\Api;

use BenSampo\Enum\Rules\EnumKey;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Brand\Entities\Brand;
use Modules\Category\Entities\Category;
use Modules\Category\Rules\CategoryRule;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Rules\ProductModelRule;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Feature\Entities\Feature;
use Modules\Media\Entities\Media;
use Modules\Product\Entities\Product;
use Modules\Product\Enums\ProductStatus;
use Modules\Product\Transformers\V1\Api\ProductCompareResource;
use Modules\Product\Transformers\V1\Api\ProductResource;
use Modules\Tag\Rules\TagRule;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/products/{category}",
     *     summary="جستجو میان مصحولات همراه با فیلتر",
     *     description="",
     *     tags={"محصولات"},
     *     @OA\Parameter(
     *         description="شناسه دسته",
     *         in="path",
     *         name="category",
     *         description="اجباری نیست",
     *         example="1",
     *         required=false,
     *         @OA\Schema(type="number"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="عملیات موفق",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function index(Request $request, $category = null)
    {
        if (!is_null($category)) $category = Category::init()->selectColumns(['id', 'name'])->findOrFailById($category);
        $latestProducts = Product::init()->search($request, $category);
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('products', ApiPaginationResource::make($latestProducts)->additional(['itemsResource' => ProductResource::class]))
            ->send();
    }

    /**
     * @OA\Get(
     *     path="/products/latest",
     *     summary="دریافت جدیدترین محصولات",
     *     description="",
     *     tags={"محصولات"},
     *     @OA\Response(
     *         response=200,
     *         description="عملیات موفق",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function latest(Request $request)
    {
        $latestProducts = Product::init()->latestProducts($request);
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('products', ApiPaginationResource::make($latestProducts)->additional(['itemsResource' => ProductResource::class]))
            ->send();
    }

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="دریافت اصلاعات محصول",
     *     description="",
     *     tags={"محصولات"},
     *     @OA\Parameter(
     *         description="شناسه محصول",
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
    public function show(Request $request, $product)
    {
        $product = Product::init()->withRateAvg()
            ->withAcceptedCommentsCount()
            ->select(['id', 'name', 'price'])
            ->with(['image', 'model'])
            ->findOrFail($product);
        $request->user()->addLastSeenProduct($product->id);
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('product', new ProductResource($product))
            ->send();
    }

    /**
     * @OA\Get(
     *     path="/products/compare/{product1}/{product2}",
     *     summary="مقایسه محصول",
     *     description="",
     *     tags={"محصولات"},
     *     @OA\Parameter(
     *         description="شناسه محصول اول",
     *         in="path",
     *         name="product1",
     *         required=true,
     *         @OA\Schema(type="number"),
     *     ),
     *     @OA\Parameter(
     *         description="شناسه محصول دوم",
     *         in="path",
     *         name="product2",
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
    public function compare($product1, $product2)
    {
        $product1 = Product::init()->selectColumns(['id', 'name', 'price'])
            ->withRelationships(['image'])
            ->withScopes(['accepted'])
            ->findOrFailById($product1);
        $product2 = Product::init()->selectColumns(['id', 'name', 'price'])
            ->withRelationships(['image'])
            ->withScopes(['accepted'])
            ->findOrFailById($product2);
        $features = Feature::init()->productCompare([$product1->id,$product2->id]);
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('product1', new ProductResource($product1))
            ->addData('product2', new ProductResource($product2))
            ->addData('features', new ProductCompareResource($features))
            ->send();
    }

    /**
     * @OA\Get(
     *     path="/products/latest/seen",
     *     summary="دریافت محصولات دیده شده",
     *     description="",
     *     tags={"محصولات"},
     *     @OA\Response(
     *         response=200,
     *         description="عملیات موفق",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function latestSeen(Request $request)
    {
        $latestSeen = Product::init()->latestSeen($request->user());
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('products', ApiPaginationResource::make($latestSeen)->additional(['itemsResource' => ProductResource::class]))
            ->send();
    }

    /**
     * @OA\Get(
     *     path="/products/most-selling-products",
     *     summary="دریافت محصولات پرفروش",
     *     description="",
     *     tags={"محصولات"},
     *     @OA\Response(
     *         response=200,
     *         description="عملیات موفق",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function mostSellingProducts(Request $request)
    {
        $mostSellingProducts = Product::init()->mostSellingProducts($request);
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('products', ApiPaginationResource::make($mostSellingProducts)->additional(['itemsResource' => ProductResource::class]))
            ->send();
    }

    public function similar(Request $request, $product)
    {
        $product=Product::init()->selectColumns(['id'])
            ->withRelationships(['tags:id'])->findOrFailById($product);
        $products = $product->getSimilarProducts($request,$product->tags->pluck('id')->toArray());
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('products', ApiPaginationResource::make($products)->additional(['itemsResource' => ProductResource::class]))
            ->send();
    }
}
