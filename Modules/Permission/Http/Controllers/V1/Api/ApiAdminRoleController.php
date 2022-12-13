<?php

namespace Modules\Permission\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Permission\Entities\Role;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAdminRoleController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function allRoles(Request $request)
    {
        return ApiResponse::message(trans('permission::messages.received_information_successfully'))
            ->addData('roles', Role::init()->allRoles())
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return ApiResponse::message(trans('permission::messages.received_information_successfully'))
            ->addData('roles', Role::init()->getAdminIndexPaginate($request))
            ->send();
    }

    /**
     * @param Request $request
     * @param $role
     * @return JsonResponse
     */
    public function show(Request $request, $role)
    {
        try {
            $role = Role::init()->findOrFailById($role);
            return ApiResponse::message(trans('permission::messages.role_was_updated'))
                ->addData('role', $role)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('permission::messages.role_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('permission::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::init($request->all(), [
            'name' => ['bail', 'required', 'string', 'min:2', Rule::unique(Role::class, 'name')],
            'name_fa' => ['bail', 'required', 'string', 'min:2'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ], [], [
            'name_fa' => trans('Name'),
            'name' => trans('English name'),
            'permissions' => trans('Permissions'),
            'permissions.*' => trans('Permission'),
        ])->validate();
        try {
            $role = Role::init()->store($request);
            return ApiResponse::message(trans('permission::messages.role_was_created'))
                ->addData('role', $role)
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('permission::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $role
     * @return JsonResponse
     */
    public function update(Request $request, $role)
    {
        ApiResponse::init($request->all(), [
            'name' => ['bail', 'required', 'string', 'min:2', Rule::unique(Role::class, 'name')->ignore($role)],
            'name_fa' => ['bail', 'required', 'string', 'min:2'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ], [], [
            'name_fa' => trans('Name'),
            'name' => trans('English name'),
            'permissions' => trans('Permissions'),
            'permissions.*' => trans('Permission'),
        ])->validate();
        try {
            $role = Role::init()->findOrFailById($role);
            $role->updateRole($request);
            return ApiResponse::message(trans('permission::messages.role_was_updated'))
                ->addData('role', $role)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('permission::messages.role_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('permission::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param $role
     * @return JsonResponse
     */
    public function destroy($role)
    {
        try {
            $role = Role::init()->findOrFailById($role);
            $role->deleteRole();
            return ApiResponse::message(trans('permission::messages.role_was_deleted'))
                ->addData('role', $role)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('permission::messages.role_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('permission::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }
}
