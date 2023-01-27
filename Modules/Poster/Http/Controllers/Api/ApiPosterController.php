<?php

namespace Modules\Poster\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Advertisement\Entities\Advertisement;
use Modules\Advertisement\Transformers\Api\ApiAdvertisementResource;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Poster\Entities\Poster;
use Modules\Poster\Transformers\Api\ApiPosterResource;

class ApiPosterController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $posters = Poster::init()->selectColumns(['id'])
            ->withScopes(['active'])
            ->withRelationships(['image'])
            ->getIndexPaginate($request);
        $resource = ApiPaginationResource::make($posters)->additional(['itemsResource' => ApiPosterResource::class]);
        return ApiResponse::message(trans("The operation was done successfully"))
            ->addData('posters', $resource)
            ->send();
    }
}
