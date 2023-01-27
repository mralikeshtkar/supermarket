<?php

namespace Modules\News\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\News\Entities\News;
use Modules\News\Entities\NewsComment;
use Symfony\Component\HttpFoundation\Response;

class ApiNewsCommentController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::init($request->all(), [
            'news_id' => ['required', 'exists:' . News::class . ',id'],
            'body' => ['required', 'string'],
        ])->validate();
        NewsComment::init()->store($request);
        return ApiResponse::message(trans("The operation was done successfully"), Response::HTTP_CREATED)->send();
    }
}
