<?php

namespace Modules\News\Http\Controllers\V1\Api\Admin;

use App\Http\Controllers\Controller;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\News\Entities\NewsCategory;
use Modules\News\Enums\NewsCategoryStatus;
use Modules\News\Transformers\Api\Admin\ApiAdminNewsCategoryResource;
use Modules\News\Transformers\Api\ApiNewsResource;
use Symfony\Component\HttpFoundation\Response;

class ApiAdminNewsCategoryController extends Controller
{

    public function index(Request $request, $newsCategory = null)
    {
        if ($newsCategory) $newsCategory = NewsCategory::init()->selectColumns(['id'])->findOrFailById($newsCategory)->id;
        $newCategories = NewsCategory::init()->selectColumns(['id', 'title', 'parent_id','status'])
            ->paginateAdmin($request, $newsCategory);
        $resource = ApiPaginationResource::make($newCategories)->additional(['itemsResource' => ApiAdminNewsCategoryResource::class]);
        return ApiResponse::message(trans("Received information successfully"))
            ->addData('newCategories', $resource)
            ->addData('statuses', collect(NewsCategoryStatus::asArray())->map(function ($item){
                return ['value'=>$item,'title'=>NewsCategoryStatus::getDescription($item)];
            })->values()->toArray())
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::init($request->all(), [
            'title' => ['required', 'string'],
            'parent_id' => ['nullable', 'exists:' . NewsCategory::class . ',id'],
            'status' => ['required', new EnumValue(NewsCategoryStatus::class)],
        ])->validate();
        NewsCategory::init()->store($request);
        return ApiResponse::message(trans("The operation was done successfully"), Response::HTTP_CREATED)->send();
    }

    /**
     * @param Request $request
     * @param $newsCategory
     * @return JsonResponse
     */
    public function update(Request $request, $newsCategory)
    {
        /** @var NewsCategory $newsCategory */
        $newsCategory = NewsCategory::init()->findOrFailById($newsCategory);
        ApiResponse::init($request->all(), [
            'title' => ['required', 'string'],
            'parent_id' => ['nullable', 'exists:' . NewsCategory::class . ',id'],
            'status' => ['required', new EnumValue(NewsCategoryStatus::class)],
        ])->validate();
        $newsCategory->updateRow($request);
        return ApiResponse::message(trans("The operation was done successfully"))->send();
    }

    /**
     * @param Request $request
     * @param $newsCategory
     * @return JsonResponse
     */
    public function destroy(Request $request, $newsCategory)
    {
        /** @var NewsCategory $newsCategory */
        $newsCategory = NewsCategory::init()->findOrFailById($newsCategory);
        $newsCategory->destroyRow();
        return ApiResponse::message(trans("The operation was done successfully"))->send();
    }

}
