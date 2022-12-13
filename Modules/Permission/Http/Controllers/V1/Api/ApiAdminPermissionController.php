<?php

namespace Modules\Permission\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Permission\Entities\Permission;

class ApiAdminPermissionController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return ApiResponse::message(trans('permission::messages.received_information_successfully'))
            ->addData('permissions', Permission::init()->getAll())
            ->send();
    }


}
