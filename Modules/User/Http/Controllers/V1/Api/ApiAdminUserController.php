<?php

namespace Modules\User\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Rules\MobileRule;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Core\Transformers\V1\Admin\AdminSidebarResource;
use Modules\Order\Transformers\Api\Admin\ApiAdminOrderResource;
use Modules\Permission\Entities\Role;
use Modules\Permission\Transformers\V1\Admin\AdminCheckUserPermissionAccessResource;
use Modules\User\Entities\User;
use Modules\User\Exports\Admin\UsersExport;
use Modules\User\Rules\UniqueMobileRule;
use Modules\User\Transformers\V1\Api\Admin\AdminUserOrderResource;
use Modules\User\Transformers\V1\Api\Admin\AdminUserResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAdminUserController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request)
    {
        ApiResponse::authorize($request->user()->can('manage', User::class));
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('user', User::init()->getUser($request))
            ->addData('permissions', new AdminCheckUserPermissionAccessResource($request->user()))
            ->addData('sidebar', new AdminSidebarResource($request->user()))
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        ApiResponse::authorize($request->user()->can('manage', User::class));
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('users', ApiPaginationResource::make(User::init()->getAdminIndexPaginate($request))->additional(['itemsResource' => AdminUserResource::class]))
            ->send();
    }

    /**
     * @param Request $request
     * @param $user
     * @return JsonResponse
     */
    public function show(Request $request, $user)
    {
        ApiResponse::authorize($request->user()->can('show', User::class));
        $user = User::init()->selectColumns(['id', 'mobile', 'email', 'name', 'password','point','code', 'is_blocked'])
            ->withRelationships(['roles'])
            ->findOrFailById($user);
        try {
            return ApiResponse::message(trans('user::messages.received_information_successfully'))
                ->addData('user', new AdminUserResource($user))
                ->addData('roles', Role::init()->allRoles())
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('user::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
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
        ApiResponse::authorize($request->user()->can('create', User::class));
        ApiResponse::init($request->all(), [
            'name' => ['nullable', 'string'],
            'code' => ['nullable', 'numeric',Rule::unique(User::class,'code')],
            'mobile' => ['required', new MobileRule(), new UniqueMobileRule()],
            'email' => ['nullable', 'email'],
        ])->validate();
        try {
            User::init()->store($request);
            return ApiResponse::message(trans('user::messages.user_was_created'))
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('user::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $user
     * @return JsonResponse
     */
    public function update(Request $request, $user)
    {
        ApiResponse::authorize($request->user()->can('edit', User::class));
        ApiResponse::init($request->all(), [
            'name' => ['nullable', 'string'],
            'code' => ['nullable', 'numeric',Rule::unique(User::class,'code')->ignore($user)],
            'mobile' => ['required', new MobileRule(), new UniqueMobileRule($user)],
            'email' => ['nullable', 'email'],
            'role' => ['nullable', 'exists:roles,name'],
            'is_blocked' => ['nullable', 'boolean'],
            'point' => ['nullable', 'numeric', 'min:0'],
        ])->validate();
        $request->merge(['is_blocked' => $request->filled('is_blocked')]);
        try {
            $user = User::init()->findOrFailById($user);
            $user->updateUser($request);
            return ApiResponse::message(trans('user::messages.user_was_updated'))
                ->addData('user', $user)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::message(trans('user::messages.user_not_found'), Response::HTTP_NOT_FOUND)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('user::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $user
     * @return JsonResponse
     */
    public function destroy(Request $request, $user)
    {
        ApiResponse::authorize($request->user()->can('destroy', User::class));
        $user = User::init()->findOrFailById($user);
        $user->deleteUser();
        return ApiResponse::message(trans('user::messages.user_was_deleted'))
            ->addData('user', $user)
            ->send();
    }

    public function exportExcel(Request $request)
    {
        $fileName = 'users-' . verta() . '.xlsx';
        $excel = Excel::raw((new UsersExport())->withFilter($request), \Maatwebsite\Excel\Excel::XLSX);
        return ApiResponse::message(trans("The operation was done successfully"))
            ->addData('name', $fileName)
            ->addData('file', "data:application/vnd.ms-excel;base64," . base64_encode($excel))
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function online(Request $request)
    {
        ApiResponse::authorize($request->user()->can('manage', User::class));
        $users = User::init()->getOnlineUsers();
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('users', ApiPaginationResource::make($users)->additional(['itemsResource' => AdminUserResource::class]))
            ->send();
    }

    /**
     * @param Request $request
     * @param $user
     * @return JsonResponse
     */
    public function cart(Request $request, $user)
    {
        ApiResponse::authorize($request->user()->can('manage', User::class));
        $user = User::init()->selectColumns(['id', 'cart'])->findOrFailById($user);
        $cart = $user->getCart();
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('cart', $cart)
            ->send();
    }

    /**
     * @param Request $request
     * @param $user
     * @return JsonResponse
     */
    public function orders(Request $request, $user)
    {
        ApiResponse::authorize($request->user()->can('manage', User::class));
        $user = User::init()->selectColumns(['id'])->findOrFailById($user);
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('orders', ApiPaginationResource::make($user->getOrders())->additional(['itemsResource' => AdminUserOrderResource::class]))
            ->send();
    }
}
