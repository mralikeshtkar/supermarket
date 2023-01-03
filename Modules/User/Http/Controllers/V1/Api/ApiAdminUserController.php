<?php

namespace Modules\User\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Rules\MobileRule;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Core\Transformers\V1\Admin\AdminSidebarResource;
use Modules\Order\Transformers\Api\Admin\ApiAdminOrderResource;
use Modules\Permission\Entities\Role;
use Modules\Permission\Transformers\V1\Admin\AdminCheckUserPermissionAccessResource;
use Modules\User\Entities\User;
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
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('users', ApiPaginationResource::make(User::init()->getAdminIndexPaginate($request))->additional(['itemsResource' => AdminUserResource::class]))
            ->send();
    }

    /**
     * @param $user
     * @return JsonResponse
     */
    public function show($user)
    {
        $user = User::init()->selectColumns(['id', 'mobile', 'email', 'name', 'password', 'is_blocked'])
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
        ApiResponse::init($request->all(), [
            'name' => ['nullable', 'string'],
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
        ApiResponse::init($request->all(), [
            'name' => ['nullable', 'string'],
            'mobile' => ['required', new MobileRule(), new UniqueMobileRule($user)],
            'email' => ['nullable', 'email'],
            'role' => ['nullable', 'exists:roles,name'],
            'is_blocked' => ['nullable', 'boolean'],
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
        try {
            $user = User::init()->findOrFailById($user);
            $user->deleteUser();
            return ApiResponse::message(trans('user::messages.user_was_deleted'))
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
     * @return JsonResponse
     */
    public function online(Request $request)
    {
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
        $user = User::init()->selectColumns(['id'])->findOrFailById($user);
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('orders', ApiPaginationResource::make($user->getOrders())->additional(['itemsResource' => AdminUserOrderResource::class]))
            ->send();
    }
}
