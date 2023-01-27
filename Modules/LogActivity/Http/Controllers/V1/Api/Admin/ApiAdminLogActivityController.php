<?php

namespace Modules\LogActivity\Http\Controllers\V1\Api\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\LogActivity\Entities\LogActivity;
use Modules\LogActivity\Transformers\V1\Api\Admin\AdminLogActivityResource;

class ApiAdminLogActivityController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        ApiResponse::authorize($request->user()->can('manage', LogActivity::class));
        $log_activities = LogActivity::init()->getAdminIndexPaginate($request);
        return ApiResponse::message(trans('Received information successfully'))
            ->addData('activities', ApiPaginationResource::make($log_activities)->additional(['itemsResource' => AdminLogActivityResource::class]))
            ->send();
    }
}
