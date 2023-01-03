<?php

namespace Modules\Permission\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Permission\Entities\Permission;
use Modules\Permission\Transformers\V1\Admin\AdminPermissionResource;

class ApiAdminPermissionController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $permissions = Permission::init()->selectColumns(['id', 'name'])->getAll();
        return ApiResponse::message(trans('permission::messages.received_information_successfully'))
            ->addData('permissions', AdminPermissionResource::collection($permissions))
            ->send();
    }

}
