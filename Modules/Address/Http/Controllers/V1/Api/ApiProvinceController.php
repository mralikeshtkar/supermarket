<?php

namespace Modules\Address\Http\Controllers\V1\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Lang;
use Modules\Address\Entities\City;
use Modules\Address\Entities\Province;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Permission\Entities\Permission;
use Modules\Permission\Enums\Roles;
use Modules\User\Entities\User;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiProvinceController extends Controller
{
    /**
     * Store a province from call api.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::authorize($request->user()->can('store', Province::class));
        ApiResponse::init($request->all(), [
            'name' => ['required', 'string', 'unique:' . Province::class . ',name'],
        ], [], trans('address::validation.attributes.' . Province::class))->validate();
        try {
            $province = Province::init()->store($request);
            return ApiResponse::message(trans('address::messages.province_was_created'))
                ->addData('province', $province)
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('address::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Find a valid province and update it's with request data.
     *
     * @param Request $request
     * @param $province
     * @return JsonResponse
     */
    public function update(Request $request, $province)
    {
        ApiResponse::authorize($request->user()->can('update', Province::class));
        ApiResponse::init($request->all(), [
            'name' => ['required', 'string', 'unique:' . Province::class . ',name,' . $province],
        ], [], trans('address::validation.attributes.' . Province::class))->validate();
        try {
            $province = Province::init()->findByColumnOrFail($province);
            $province = $province->updateProvince($request);
            return ApiResponse::message(trans('address::messages.province_was_updated'))
                ->addData('province', $province)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('address::messages.province_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('feature::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Find a province with its cities.
     *
     * @param Request $request
     * @param $province
     * @return JsonResponse
     */
    public function show(Request $request, $province)
    {
        try {
            $province = Province::init()->findByColumnOrFail($province);
            $province->load('cities');
            return ApiResponse::message(trans('address::messages.received_information_successfully'))
                ->addData('province', $province)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('address::messages.province_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('feature::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Delete a province from call api.
     *
     * @param Request $request
     * @param $province
     * @return JsonResponse
     */
    public function destroy(Request $request, $province)
    {
        ApiResponse::authorize($request->user()->can('destroy', Province::class));
        try {
            $province = Province::init()->findByColumnOrFail($province);
            $province->destroyProvince();
            return ApiResponse::message(trans('address::messages.province_was_deleted'))
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('address::messages.province_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('address::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @return JsonResponse
     */
    public function provinces()
    {
        try {
            return ApiResponse::message(trans('address::messages.received_information_successfully'))
                ->addData('provinces',Province::init()->getProvinces())
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('address::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }
}
