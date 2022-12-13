<?php

namespace Modules\Category\Http\Controllers\V1\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Category\Entities\Category;
use Modules\Category\Enums\CategoryStatus;
use Modules\Category\Transformers\V1\Api\CategoryFilterResource;
use Modules\Category\Transformers\V1\Api\CategoryResource;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Permission\Entities\Permission;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiCategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/categories/{id}",
     *     summary="دریافت دسته بندی",
     *     description="",
     *     tags={"دسته بندی"},
     *     @OA\Parameter(
     *         description="میتواند خالی باشد",
     *         in="path",
     *         name="id",
     *         example="1",
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
    public function categories(Request $request, $category)
    {
        if ($category) $category = Category::init()->withScopes(['accepted'])->findOrFailById($category);
        $categories = Category::init()->getAllCategories($request,$category);
        return ApiResponse::message(trans('category::messages.received_information_successfully'))
            ->addData('categories', ApiPaginationResource::make($categories)->additional(['itemsResource' => CategoryResource::class]))
            ->send();
    }

    /**
     * @param Request $request
     * @param $slug
     * @return JsonResponse
     */
    public function products(Request $request, $slug)
    {
        try {
            $category = Category::init()->findOrFailWithSlug($slug);
            return ApiResponse::message(trans('category::messages.received_information_successfully'))
                ->addData('category', $category)
                ->addData('products', $category->products()->paginate())
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('category::messages.category_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('category::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Change status a category to accepted.
     *
     * @param Request $request
     * @param $slug
     * @return JsonResponse
     */
    public function accept(Request $request, $slug)
    {
        ApiResponse::authorize($request->user()->can('action', Permission::class));
        return $this->_changeStatus($slug, CategoryStatus::Accepted);
    }

    /**
     * Change status a category to rejected.
     *
     * @param Request $request
     * @param $slug
     * @return JsonResponse
     */
    public function reject(Request $request, $slug)
    {
        ApiResponse::authorize($request->user()->can('action', Permission::class));
        return $this->_changeStatus($slug, CategoryStatus::Rejected);
    }

    /**
     * Show category features.
     *
     * @param Request $request
     * @param $slug
     * @return JsonResponse
     */
    public function features(Request $request, $slug)
    {
        ApiResponse::authorize($request->user()->can('manage', Permission::class));
        try {
            $category = Category::init()->findOrFailWithSlug($slug);
            $category->load('features');
            return ApiResponse::message(trans('category::messages.received_information_successfully'))
                ->addData('category', $category)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('category::messages.category_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('category::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @OA\Get(
     *     path="/categories/{id}/filters",
     *     summary="لیست فیلترهای دسته",
     *     description="",
     *     tags={"دسته بندی"},
     *     @OA\Parameter(
     *         description="شناسه دسته",
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
    public function filters(Request $request, $category)
    {
        $category = Category::init()->selectColumns(['id', 'name'])
            ->withRelationships(['image'])
            ->findOrFailById($category);
        return ApiResponse::message(trans('category::messages.received_information_successfully'))
            ->addData('category', new CategoryResource($category))
            ->addData('filters', new CategoryFilterResource($category->filters()))
            ->send();
    }

    /**
     * Manage change status a category.
     *
     * @param $slug
     * @param $status
     * @return JsonResponse
     */
    private function _changeStatus($slug, $status): JsonResponse
    {
        try {
            $category = Category::init()->findOrFailWithSlug($slug);
            Category::init()->changeStatus($category, $status);
            return ApiResponse::message(trans('category::messages.category_was_updated'))
                ->addData('category', [
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'status' => $category->getStatus(),
                ])
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('category::messages.category_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('category::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }
}
