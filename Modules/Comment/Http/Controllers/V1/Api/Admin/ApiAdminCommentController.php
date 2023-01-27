<?php

namespace Modules\Comment\Http\Controllers\V1\Api\Admin;

use App\Http\Controllers\Controller;
use BenSampo\Enum\Rules\EnumKey;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Comment\Entities\Comment;
use Modules\Comment\Enums\CommentStatus;
use Modules\Comment\Rules\CommentableRule;
use Modules\Comment\Rules\CommentAcceptedRule;
use Modules\Comment\Transformers\V1\Api\Admin\AdminCommentResource;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use function trans;

class ApiAdminCommentController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        ApiResponse::authorize($request->user()->can('manage', Comment::class));
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('comments', ApiPaginationResource::make(Comment::init()->getAdminIndexPaginate($request))->additional(['itemsResource' => AdminCommentResource::class]))
            ->send();
    }

    /**
     * @param Request $request
     * @param $comment
     * @return JsonResponse
     */
    public function show(Request $request, $comment)
    {
        ApiResponse::authorize($request->user()->can('show', Comment::class));
        $comment = Comment::init()->findByIdOrFail($comment);
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('comment', new AdminCommentResource($comment))
            ->send();
    }

    /**
     * @param Request $request
     * @param $comment
     * @return JsonResponse
     */
    public function update(Request $request, $comment)
    {
        ApiResponse::authorize($request->user()->can('edit', Comment::class));
        $comment = Comment::init()->findForShow($comment);
        $request->merge(['commentable' => [
            'type' => Relation::getMorphedModel(strtolower(optional($request->commentable)['type'])),
            'id' => optional($request->commentable)['id'],
        ]]);
        ApiResponse::init($request->all(), [
            'commentable' => ['bail', 'required', 'array:id,type', new CommentableRule()],
            'parent_id' => ['bail', 'nullable', 'exists:' . Comment::class . ',id', new CommentAcceptedRule()],
            'body' => ['bail', 'required', 'string', 'min:3'],
            'title' => ['bail', 'required', 'string', 'min:3'],
            'rate' => ['bail', 'nullable', 'numeric', 'min:1', 'max:5'],
            'advantage' => ['bail', 'nullable', 'array'],
            'advantage.*' => ['string'],
            'disadvantage' => ['bail', 'nullable', 'array'],
            'disadvantage.*' => ['string'],
        ], [], trans('comment::validation.attributes'))->validate();
        $comment->updateItem($request);
        return ApiResponse::message(trans('comment::messages.comment_was_updated'))->send();
    }

    /**
     * @param Request $request
     * @param $comment
     * @return JsonResponse
     */
    public function destroy(Request $request, $comment)
    {
        ApiResponse::authorize($request->user()->can('destroy', Comment::class));
        $comment = Comment::init()->findByIdOrFail($comment);
        $comment->destroyItem();
        return ApiResponse::message(trans('comment::messages.comment_was_deleted'))->send();
    }

    /**
     * @param Request $request
     * @param $comment
     * @return JsonResponse
     */
    public function changeStatus(Request $request, $comment)
    {
        ApiResponse::authorize($request->user()->can('changeStatus', Comment::class));
        $comment = Comment::init()->findByIdOrFail($comment);
        try {
            $comment = $comment->updateStatus();
            return ApiResponse::message(trans('comment::messages.status_comment_changed'))
                ->addData('comment', new AdminCommentResource($comment))
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('comment::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->hasError()
                ->send();
        }
    }
}
