<?php

namespace Modules\Tag\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Tag\Entities\Tag;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAdminTagController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request)
    {
        try {
            return ApiResponse::message(trans('tag::messages.received_information_successfully'))
                ->addData('tags', Tag::init()->allTags($request))
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('tag::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
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
        ApiResponse::authorize($request->user()->can('manage', Tag::class));
        try {
            return ApiResponse::message(trans('tag::messages.received_information_successfully'))
                ->addData('tags', Tag::init()->getAdminIndexPaginate($request))
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('tag::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->hasError()
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $tag
     * @return JsonResponse
     */
    public function show(Request $request, $tag)
    {
        ApiResponse::authorize($request->user()->can('show', Tag::class));
        $tag = Tag::init()->findOrFailById($tag);
        return ApiResponse::message(trans('tag::messages.received_information_successfully'))
            ->addData('tag', $tag)
            ->send();
    }

    /**
     * Destroy a tag
     *
     * @param Request $request
     * @param $tag
     * @return JsonResponse
     */
    public function destroy(Request $request, $tag)
    {
        ApiResponse::authorize($request->user()->can('destroy', Tag::class));
        $tag = Tag::init()->findOrFailById($tag);
        Tag::init()->destroyTag($tag);
        return ApiResponse::message(trans('tag::messages.tag_was_deleted'))->send();
    }

    /**
     * Store a tag with request data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::authorize($request->user()->can('store', Tag::class));
        $request->merge(['slug' => Str::slug($request->get('slug'))]);
        ApiResponse::init($request->all(), [
            'name' => ['required', 'string', 'unique:' . Tag::class . ',name'],
            'slug' => ['required', 'string', 'unique:' . Tag::class . ',slug'],
        ], [], trans('tag::validation.attributes'))->validate();
        $tag = Tag::init()->store($request);
        return ApiResponse::message(trans('tag::messages.tag_was_created'))
            ->addData('name', $tag->name)
            ->addData('slug', $tag->slug)
            ->send();
    }

    /**
     * @param Request $request
     * @param $tag
     * @return JsonResponse
     */
    public function update(Request $request, $tag)
    {
        ApiResponse::authorize($request->user()->can('edit', Tag::class));
        $request->merge(['slug' => Str::slug($request->get('slug'))]);
        ApiResponse::init($request->all(), [
            'name' => ['required', 'string', Rule::unique(Tag::class, 'name')->ignore($tag)],
            'slug' => ['required', 'string', Rule::unique(Tag::class, 'slug')->ignore($tag)],
        ], [], trans('tag::validation.attributes'))->validate();
        $tag = Tag::init()->findOrFailById($tag);
        $tag = Tag::init()->updateTag($tag, $request);
        return ApiResponse::message(trans('tag::messages.tag_was_updated'))
            ->addData('name', $tag->name)
            ->addData('slug', $tag->slug)
            ->send();
    }
}
