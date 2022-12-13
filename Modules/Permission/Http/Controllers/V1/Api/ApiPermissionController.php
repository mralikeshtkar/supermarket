<?php

namespace Modules\Permission\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Permission\Entities\Permission;
use Modules\Permission\Transformers\PermissionPaginateCollection;

class ApiPermissionController extends Controller
{
    /**
     * Permitted user can paginate permissions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function permissions(Request $request)
    {
        ApiResponse::authorize($request->user()->can('manage', Permission::class));
        return ApiResponse::message(trans('permission::messages.received_information_successfully'))
            ->addData('permissions', Permission::query()->paginate())
            ->send();
    }

}
