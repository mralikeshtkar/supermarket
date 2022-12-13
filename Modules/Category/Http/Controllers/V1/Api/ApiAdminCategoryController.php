<?php

namespace Modules\Category\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Category\Entities\Category;
use Modules\Category\Transformers\V1\Api\Admin\AdminCategoryResource;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Feature\Entities\Feature;
use Modules\Feature\Transformers\V1\Api\Admin\AdminFeatureResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAdminCategoryController extends Controller
{
    /**
     * @param Request $request
     * @param $category
     * @return JsonResponse
     */
    public function index(Request $request, $category = null)
    {
        dd("salam");
        return ApiResponse::message(trans('category::messages.received_information_successfully'))
            ->addData('categories', Category::init()->getAdminIndexPaginate($request, $category))
            ->send();
    }

    /**
     * @param $category
     * @return JsonResponse
     */
    public function show($category)
    {
        try {
            $category = Category::init()->findOrFailById($category);
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
     * @return JsonResponse
     */
    public function all()
    {
        try {
            return ApiResponse::message(trans('category::messages.received_information_successfully'))
                ->addData('categories', Category::init()->allCategories())
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('category::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $category
     * @param $feature
     * @return JsonResponse
     */
    public function features(Request $request, $category, $feature = null)
    {
        $category = Category::init()->findOrFailById($category);
        if ($feature) $feature = Feature::init()->findByColumnOrFail($feature);
        $parent_features = $category->features()->select([
            'id',
            'featureable_id',
            'featureable_type',
            'title',
            'has_option',
            'is_filter',
        ])->when($feature, function (Builder $builder) use ($feature) {
            $builder->where('parent_id', $feature->id);
        }, function (Builder $builder) use ($feature) {
            $builder->whereNull('parent_id');
        })->paginate();
        return ApiResponse::message(trans('category::messages.received_information_successfully'))
            ->addData('features', ApiPaginationResource::make($parent_features)->additional(['itemsResource' => AdminFeatureResource::class]))
            ->send();
    }

    /**
     * Store a category.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::init($request->all(), [
            'name' => ['required', 'string', 'unique:' . Category::class . ',name'],
            'slug' => ['required', 'string', 'unique:' . Category::class . ',slug'],
            'parent_id' => ['nullable', 'exists:' . Category::class . ',id'],
            'image' => ['nullable', 'image'],
        ], [], trans('category::validation.attributes'))->validate();
        try {
            $category = Category::init()->store($request);
            return ApiResponse::message(trans('category::messages.category_was_created'))
                ->addData('category', $category)
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('category::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Update a category.
     *
     * @param Request $request
     * @param $slug
     * @return JsonResponse
     */
    public function update(Request $request, $category)
    {
        ApiResponse::init($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique(Category::class, 'name')
                    ->ignore($category)
            ],
            'slug' => [
                'required',
                'string',
                Rule::unique(Category::class, 'slug')
                    ->ignore($category)
            ],
            'parent_id' => [
                'nullable',
                Rule::exists(Category::class, 'id'),
            ],
        ], [], trans('category::validation.attributes'))->validate();
        try {
            $category = Category::init()->findOrFailById($category);
            $category = Category::init()->updateCategory($category, $request);
            return ApiResponse::message(trans('category::messages.category_was_updated'))
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
     * Delete a category.
     *
     * @param Request $request
     * @param $category
     * @return JsonResponse
     */
    public function destroy(Request $request, $category)
    {
        try {
            $category = Category::init()->findOrFailById($category);
            Category::init()->destroyCategory($category);
            return ApiResponse::message(trans('category::messages.category_was_deleted'))->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('category::messages.category_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('category::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function accepted(Request $request)
    {
        try {
            return ApiResponse::message(trans('category::messages.received_information_successfully'))
                ->addData('categories', Category::init()->onlyAccepted($request))
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('category::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }
}
