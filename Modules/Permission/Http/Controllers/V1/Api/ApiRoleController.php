<?php

namespace Modules\Permission\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Permission\Entities\Role;

class ApiRoleController extends Controller
{
    /**
     * Permitted user can paginate permissions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function roles(Request $request)
    {
        ApiResponse::authorize($request->user()->can('manage', Role::class));
        return ApiResponse::message(trans('permission::messages.received_information_successfully'))
            ->addData('roles', Role::query()->paginate())
            ->send();
    }

}
