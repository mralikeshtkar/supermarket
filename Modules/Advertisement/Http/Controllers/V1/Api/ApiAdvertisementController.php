<?php

namespace Modules\Advertisement\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Advertisement\Entities\Advertisement;
use Modules\Advertisement\Transformers\Api\Admin\ApiAdminAdvertisementResource;
use Modules\Advertisement\Transformers\Api\ApiAdvertisementResource;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;

class ApiAdvertisementController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $advertisements = Advertisement::init()->selectColumns(['id', 'place',])
            ->withScopes(['active'])
            ->withRelationships(['image'])
            ->getIndexPaginate($request);
        $resource = ApiPaginationResource::make($advertisements)->additional(['itemsResource' => ApiAdvertisementResource::class]);
        return ApiResponse::message(trans("The operation was done successfully"))
            ->addData('news', $resource)
            ->send();
    }
}
