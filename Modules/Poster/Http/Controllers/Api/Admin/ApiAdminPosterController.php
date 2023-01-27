<?php

namespace Modules\Poster\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Poster\Entities\Poster;
use Modules\Poster\Enums\PosterStatus;
use Modules\Poster\Transformers\Api\Admin\ApiAdminPosterResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAdminPosterController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $posters = Poster::init()->selectColumns(['id', 'status', 'created_at'])
            ->withRelationships(['image'])
            ->paginateAdmin($request);
        $resource = ApiPaginationResource::make($posters)->additional(['itemsResource' => ApiAdminPosterResource::class]);
        return ApiResponse::message(trans("The operation was done successfully"))
            ->addData('advertisements', $resource)
            ->send();
    }

    /**
     * @param Request $request
     * @param $poster
     * @return JsonResponse
     */
    public function show(Request $request, $poster)
    {
        $poster = Poster::init()->selectColumns(['id', 'status', 'created_at'])
            ->withRelationships(['image'])
            ->findOrFailById($poster);
        return ApiResponse::message(trans("The operation was done successfully"))
            ->addData('poster', new ApiAdminPosterResource($poster))
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::init($request->all(), [
            'image' => ['required', 'image'],
            'status' => ['required', new EnumValue(PosterStatus::class)],
        ])->validate();
        try {
            return DB::transaction(function () use ($request) {
                Poster::init()->store($request);
                return ApiResponse::message(trans("The operation was done successfully"), Response::HTTP_CREATED)->send();
            });
        } catch (Throwable $e) {
            return ApiResponse::sendError(trans("Internal server error"));
        }
    }

    /**
     * @param Request $request
     * @param $poster
     * @return JsonResponse|mixed
     */
    public function update(Request $request, $poster)
    {
        /** @var Poster $poster */
        $poster = Poster::init()->withRelationships(['image'])->findOrFailById($poster);
        ApiResponse::init($request->all(), [
            'image' => ['nullable', 'image'],
            'status' => ['required', new EnumValue(PosterStatus::class)],
        ])->validate();
        try {
            return DB::transaction(function () use ($request,$poster) {
                $poster->updateRow($request);
                return ApiResponse::message(trans("The operation was done successfully"), Response::HTTP_CREATED)->send();
            });
        } catch (Throwable $e) {
            return ApiResponse::sendError(trans("Internal server error"));
        }
    }

    /**
     * @param Request $request
     * @param $poster
     * @return JsonResponse|mixed
     */
    public function destroy(Request $request, $poster)
    {
        /** @var Poster $poster */
        $poster = Poster::init()->withRelationships(['image'])->findOrFailById($poster);
        try {
            return DB::transaction(function () use ($request,$poster) {
                $poster->destroyRow();
                return ApiResponse::message(trans("The operation was done successfully"), Response::HTTP_CREATED)->send();
            });
        } catch (Throwable $e) {
            return ApiResponse::sendError(trans("Internal server error"));
        }
    }
}
