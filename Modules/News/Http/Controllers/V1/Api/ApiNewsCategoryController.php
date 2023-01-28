<?php

namespace Modules\News\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\News\Entities\NewsCategory;
use Modules\News\Transformers\Api\ApiNewsCategoryResource;
use Modules\News\Transformers\Api\ApiNewsResource;

class ApiNewsCategoryController extends Controller
{
    public function index(Request $request, $newsCategory = null)
    {
        if ($newsCategory) $newsCategory = NewsCategory::init()->selectColumns(['id'])->findOrFailById($newsCategory)->id;
        $newsCategories = NewsCategory::init()->selectColumns(['id', 'title','parent_id'])
            ->withScopes(['accepted'])
            ->getIndex($request, $newsCategory);
        return ApiResponse::message(trans("Received information successfully"))
            ->addData('newCategories', $newsCategories)
            ->send();
    }

    /**
     * @param Request $request
     * @param $newsCategory
     * @return JsonResponse
     */
    public function news(Request $request, $newsCategory)
    {
        /** @var NewsCategory $newsCategory */
        $newsCategory = NewsCategory::init()->withScopes(['accepted'])
            ->findOrFailById($newsCategory);
        $news = $newsCategory->getPaginateNews($request);
        $resource = ApiPaginationResource::make($news)->additional(['itemsResource' => ApiNewsResource::class]);
        return ApiResponse::message(trans("Received information successfully"))
            ->addData('newsCategory', new ApiNewsCategoryResource($newsCategory))
            ->addData('news', $resource)
            ->send();
    }
}
