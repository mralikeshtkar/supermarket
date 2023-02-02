<?php

namespace Modules\Transportation\Http\Controllers\V1\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Setting\Entities\Setting;
use Modules\Transportation\Entities\Transportation;
use Modules\Transportation\Transformers\Api\Admin\ApiAdminTransportationResource;
use Symfony\Component\HttpFoundation\Response;

class ApiAdminTransportationController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $transportations = Transportation::init()->selectColumns(['id','title'])
            ->paginateAdmin($request);
        $resource = ApiPaginationResource::make($transportations)->additional(['itemsResource' => ApiAdminTransportationResource::class]);
        return ApiResponse::message(trans("The operation was done successfully"))
            ->addData('transportations', $resource)
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::init($request->all(), [
            'title' => ['required', 'string'],
        ])->validate();
        Transportation::init()->store($request);
        return ApiResponse::message(trans("The operation was done successfully"), Response::HTTP_CREATED)->send();
    }

    /**
     * @param Request $request
     * @param $transportation
     * @return JsonResponse
     */
    public function update(Request $request, $transportation)
    {
        /** @var Transportation $transportation */
        $transportation = Transportation::init()->findOrFailById($transportation);
        ApiResponse::init($request->all(), [
            'title' => ['required', 'string'],
        ])->validate();
        $transportation->updateRow($request);
        return ApiResponse::message(trans("The operation was done successfully"))->send();
    }

    /**
     * @param Request $request
     * @param $transportation
     * @return JsonResponse
     */
    public function destroy(Request $request, $transportation)
    {
        /** @var Transportation $transportation */
        $transportation = Transportation::init()->findOrFailById($transportation);
        $transportation->destroyRow();
        return ApiResponse::message(trans("The operation was done successfully"))->send();
    }
}
