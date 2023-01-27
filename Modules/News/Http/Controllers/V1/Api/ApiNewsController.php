<?php

namespace Modules\News\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\News\Entities\News;
use Modules\News\Transformers\Api\ApiCommentResource;
use Modules\News\Transformers\Api\ApiNewsResource;

class ApiNewsController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $news = News::init()->selectColumns(['id', 'user_id', 'category_id', 'title', 'body', 'created_at'])
            ->withRelationships(['user:id,name,email', 'newsCategory:id,title'])
            ->withScopes(['accepted'])
            ->getIndexPaginate($request);
        $resource = ApiPaginationResource::make($news)->additional(['itemsResource' => ApiNewsResource::class]);
        return ApiResponse::message(trans("Received information successfully"))
            ->addData('newCategories', $resource)
            ->send();
    }

    /**
     * @param Request $request
     * @param $news
     * @return JsonResponse
     */
    public function comments(Request $request, $news)
    {
        /** @var News $news */
        $news = News::init()->selectColumns(['id'])
            ->withScopes(['accepted'])
            ->findOrFailById($news);
        $comments = $news->selectColumns(['id', 'user_id', 'body', 'created_at'])
            ->withRelationships(['user:id,name,email'])
            ->withScopes(['accepted'])
            ->getPaginateComments($request);
        $resource = ApiPaginationResource::make($comments)->additional(['itemsResource' => ApiCommentResource::class]);
        return ApiResponse::message(trans("Received information successfully"))
            ->addData('comments', $resource)
            ->send();
    }
}
