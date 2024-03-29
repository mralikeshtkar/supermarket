<?php

namespace Modules\News\Http\Controllers\V1\Api\Admin;

use App\Http\Controllers\Controller;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\News\Entities\News;
use Modules\News\Entities\NewsComment;
use Modules\News\Enums\NewsCommentStatus;
use Modules\News\Transformers\Api\Admin\ApiAdminCommentResource;
use Modules\News\Transformers\Api\Admin\ApiAdminNewsCategoryResource;
use Modules\Product\Entities\Product;

class ApiAdminNewsCommentController extends Controller
{
    /**
     * @param Request $request
     * @param $news
     * @return JsonResponse
     */
    public function index(Request $request,$news)
    {
        $news = News::init()->selectColumns(['id'])->findOrFailById($news);
        $comments = NewsComment::init()->selectColumns(['id', 'user_id', 'news_id', 'body', 'status', 'created_at'])
            ->withRelationships(['user:id,name,email'])
            ->paginateAdminByNewsId($request,$news->id);
        $resource = ApiPaginationResource::make($comments)->additional(['itemsResource' => ApiAdminCommentResource::class]);
        return ApiResponse::message(trans("Received information successfully"))
            ->addData('comments', $resource)
            ->addData('statuses', collect(NewsCommentStatus::asArray())->map(function ($item){
                return ['value'=>$item,'title'=>NewsCommentStatus::getDescription($item)];
            })->values()->toArray())
            ->send();
    }

    /**
     * @param Request $request
     * @param $newsComment
     * @return JsonResponse
     */
    public function show(Request $request, $newsComment)
    {
        $comment = NewsComment::init()->selectColumns(['id', 'user_id', 'news_id', 'body', 'status', 'created_at'])
            ->withRelationships(['user:id,name,email'])
            ->findOrFailById($newsComment);
        return ApiResponse::message(trans("Received information successfully"))
            ->addData('comment', new ApiAdminNewsCategoryResource($comment))
            ->send();
    }

    /**
     * @param Request $request
     * @param $newsComment
     * @return JsonResponse
     */
    public function update(Request $request, $newsComment)
    {
        /** @var NewsComment $newsComment */
        $newsComment = NewsComment::init()->findOrFailById($newsComment);
        ApiResponse::init($request->all(), [
            'body' => ['required', 'string'],
            'status' => ['required', new EnumValue(NewsCommentStatus::class)],
        ])->validate();
        $newsComment->updateRow($request);
        return ApiResponse::message(trans("The operation was done successfully"))->send();
    }

    /**
     * @param Request $request
     * @param $newsComment
     * @return JsonResponse
     */
    public function destroy(Request $request, $newsComment)
    {
        /** @var NewsComment $newsComment */
        $newsComment = NewsComment::init()->selectColumns(['id'])->findOrFailById($newsComment);
        $newsComment->destroyRow();
        return ApiResponse::message(trans("The operation was done successfully"))->send();
    }
}
