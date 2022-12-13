<?php

namespace Modules\Tag\Http\Controllers\V1\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Tag\Entities\Tag;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiTagController extends Controller
{

    /**
     * Show tag with products.
     *
     * @param $slug
     * @return JsonResponse
     */
    public function show($slug)
    {
        try {
            $tag = Tag::init()->findOrFailWithSlug($slug,['products']);
            return ApiResponse::message(trans('tag::messages.received_information_successfully'))
                ->addData('tag',$tag)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::message(trans('tag::messages.tag_not_found'), Response::HTTP_NOT_FOUND)
                ->addError('message', $e->getMessage())
                ->hasError()
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('tag::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->hasError()
                ->send();
        }
    }
}
