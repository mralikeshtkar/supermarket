<?php

namespace Modules\News\Http\Controllers\V1\Api\Admin;

use App\Http\Controllers\Controller;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\News\Entities\News;
use Modules\News\Entities\NewsCategory;
use Modules\News\Enums\NewsStatus;
use Modules\News\Transformers\Api\Admin\ApiAdminNewsResource;
use Symfony\Component\HttpFoundation\Response;

class ApiAdminNewsController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $news = News::init()->selectColumns(['id', 'user_id', 'category_id', 'title', 'body','status', 'created_at'])
            ->withRelationships(['user:id,name,email', 'newsCategory:id,title'])
            ->paginateAdmin($request);
        $resource = ApiPaginationResource::make($news)->additional(['itemsResource' => ApiAdminNewsResource::class]);
        return ApiResponse::message(trans("The operation was done successfully"))
            ->addData('news', $resource)
            ->addData('newsCategories', NewsCategory::init()->selectColumns(['id','title'])->withScopes(['accepted'])->getData())
            ->addData('statuses', collect(NewsStatus::asArray())->map(function ($item) {
                return [
                    'title' => NewsStatus::getDescription($item),
                    'value' => $item,
                ];
            })->values())
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
            'body' => ['required', 'string'],
            'news_category_id' => ['nullable', 'exists:' . NewsCategory::class . ',id'],
            'status' => ['required', new EnumValue(NewsStatus::class)],
        ])->validate();
        News::init()->store($request);
        return ApiResponse::message(trans("The operation was done successfully"), Response::HTTP_CREATED)->send();
    }

    /**
     * @param Request $request
     * @param $news
     * @return JsonResponse
     */
    public function update(Request $request, $news)
    {
        /** @var News $news */
        $news = News::init()->findOrFailById($news);
        ApiResponse::init($request->all(), [
            'title' => ['required', 'string'],
            'body' => ['required', 'string'],
            'news_category_id' => ['nullable', new EnumValue(NewsStatus::class)],
            'status' => ['required', new EnumValue(NewsStatus::class)],
        ])->validate();
        $news->updateRow($request);
        return ApiResponse::message(trans("The operation was done successfully"))->send();
    }

    public function destroy(Request $request, $news)
    {
        /** @var News $news */
        $news = News::init()->selectColumns(['id'])->findOrFailById($news);
        $news->destroyRow();
        return ApiResponse::message(trans("The operation was done successfully"))->send();
    }
}
