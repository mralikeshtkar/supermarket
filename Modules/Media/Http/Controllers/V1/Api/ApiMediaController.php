<?php

namespace Modules\Media\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Media\Rules\MediaModelRule;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ApiMediaController extends Controller
{
    /**
     * @param Request $request
     * @param $media
     * @param $collection
     * @return JsonResponse
     */
    public function destroy(Request $request, $media, $collection)
    {
        $request->merge([
            'model' => [
                'id' => optional($request->get('model'))->offsetGet('id'),
                'type' => Relation::getMorphedModel(optional($request->get('model'))->offsetGet('type')),
            ],
        ]);
        ApiResponse::init($request->all(), [
            'model' => ['required', 'array:id,type',new MediaModelRule($request)],
        ])->validate();
        ApiResponse::authorize($request->user()->can($collection, $request->model['type']));
        try {
            $model = $request->model['type']::findByIdWithCollection($request->model,$request)->firstOrFail();
            $model->media->first()->delete();
            return ApiResponse::sendMessage(trans('media::messages.media_was_delete'));
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::sendError(trans('media::messages.media_not_found'), HttpResponse::HTTP_NOT_FOUND);
        } catch (\Throwable $exception) {
            return ApiResponse::sendError(trans('media::messages.internal_error'), HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
