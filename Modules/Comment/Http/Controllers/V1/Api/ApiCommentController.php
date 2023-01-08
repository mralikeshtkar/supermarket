<?php

namespace Modules\Comment\Http\Controllers\V1\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Comment\Entities\Comment;
use Modules\Comment\Rules\CommentableRule;
use Modules\Comment\Rules\CommentAcceptedRule;
use Modules\Comment\Transformers\V1\Api\CommentResource;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiCommentController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->merge(['commentable' => [
            'id' => $request->commentable['id'],
            'type' => Relation::getMorphedModel(strtolower($request->commentable['type'])),
        ]]);
        ApiResponse::init($request->all(), [
            'commentable' => ['bail', 'required', 'array:id,type', new CommentableRule()],
            'parent_id' => ['bail', 'nullable', 'exists:' . Comment::class . ',id', new CommentAcceptedRule()],
            'title' => ['bail', 'required', 'string', 'min:3'],
            'body' => ['bail', 'required', 'string', 'min:3'],
            'rate' => ['bail', 'nullable', 'numeric', 'min:1', 'max:5'],
            'advantage' => ['bail', 'nullable', 'array'],
            'advantage.*' => ['string'],
            'disadvantage' => ['bail', 'nullable', 'array'],
            'disadvantage.*' => ['string'],
        ], [], trans('comment::validation.attributes'))->validate();
        try {
            $commentable = $request->commentable['type']::init()->findByColumnOrFail($request->commentable['id']);
            $commentable->storeComment($request);
            return ApiResponse::message(trans('comment::messages.comment_was_created'))->send();
        } catch (Throwable $e) {
            dd($e);
            return ApiResponse::message(trans('comment::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->send();
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $request->merge(['commentable' => [
            'id' => $request->commentable['id'],
            'type' => Relation::getMorphedModel(strtolower($request->commentable['type'])),
        ]]);
        ApiResponse::init($request->all(), [
            'commentable' => ['bail', 'required', 'array:id,type', new CommentableRule()],
        ], [], [
            'commentable' => trans('Entered information'),
        ])->validate();
        try {
            $commentable = $request->commentable['type']::init()->findByColumnOrFail($request->commentable['id']);
            $comments = $commentable->comments()->select([
                'user_id', 'commentable_id','created_at', 'commentable_type', 'title', 'body', 'advantage', 'disadvantage', 'rate'
            ])->with(['user:id,name,email,mobile'])->accepted()->paginate();
            return ApiResponse::message(trans('comment::messages.received_information_successfully'))
                ->addData('comments', ApiPaginationResource::make($comments)->additional(['itemsResource' => CommentResource::class]))
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('comment::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->hasError()
                ->send();
        }
    }

}
