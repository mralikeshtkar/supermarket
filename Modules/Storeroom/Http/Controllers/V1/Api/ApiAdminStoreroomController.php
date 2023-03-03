<?php

namespace Modules\Storeroom\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Rules\PhoneNumberRule;
use Modules\Storeroom\Entities\Storeroom;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAdminStoreroomController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        ApiResponse::authorize($request->user()->can('manage', Storeroom::class));
        return ApiResponse::message(trans('storeroom::messages.received_information_successfully'))
            ->addData('storerooms', Storeroom::init()->getAdminIndexPaginate($request))
            ->send();
    }

    /**
     * @param Request $request
     * @param $storeroom
     * @return JsonResponse
     */
    public function show(Request $request,$storeroom)
    {
        ApiResponse::authorize($request->user()->can('manage', Storeroom::class));
        $storeroom = Storeroom::init()->findByIdOrFail($storeroom);
        return ApiResponse::message(trans('storeroom::messages.received_information_successfully'))
            ->addData('storeroom', $storeroom)
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::authorize($request->user()->can('create', Storeroom::class));
        ApiResponse::init($request->all(), [
            'province_id' => ['required', 'exists:provinces,id'],
            'city_id' => ['required', Rule::exists('cities', 'id')->where('province_id', $request->province_id)],
            'name' => ['required', 'string', 'unique:storerooms,name'],
            'address' => ['required', 'string'],
            'phone_numbers' => ['nullable', 'array'],
            'phone_numbers.*' => [new PhoneNumberRule()],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ], [], [
            'phone_numbers' => trans('Phone numbers'),
            'phone_numbers.*' => trans('Phone number'),
            'province_id' => trans('Province'),
            'city_id' => trans('City'),
            'lat' => trans('Latitude'),
            'lng' => trans('Longitude'),
        ])->validate();
        try {
            Storeroom::init()->store($request);
            return ApiResponse::message(trans('storeroom::messages.storeroom_was_created'))->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('storeroom::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $storeroom
     * @return JsonResponse
     */
    public function update(Request $request, $storeroom)
    {
        ApiResponse::authorize($request->user()->can('edit', Storeroom::class));
        ApiResponse::init($request->all(), [
            'province_id' => ['required', 'exists:provinces,id'],
            'city_id' => ['required', Rule::exists('cities', 'id')->where('province_id', $request->province_id)],
            'name' => ['required', 'string', 'unique:storerooms,name,' . $storeroom],
            'address' => ['required', 'string'],
            'phone_numbers' => ['nullable', 'array'],
            'phone_numbers.*' => [new PhoneNumberRule()],
        ], [], [
            'phone_numbers' => trans('Phone numbers'),
            'phone_numbers.*' => trans('Phone number'),
            'province_id' => trans('Province'),
            'city_id' => trans('City'),
        ])->validate();
        try {
            $storeroom = Storeroom::init()->findByIdOrFail($storeroom);
            $storeroom->updateStoreroom($request);
            return ApiResponse::message(trans('storeroom::messages.storeroom_was_updated'))->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('storeroom::messages.storeroom_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('storeroom::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $storeroom
     * @return JsonResponse
     */
    public function destroy(Request $request, $storeroom)
    {
        ApiResponse::authorize($request->user()->can('destroy', Storeroom::class));
        $storeroom = Storeroom::init()->findByIdOrFail($storeroom);
        $storeroom->delete();
        return ApiResponse::sendMessage(trans('storeroom::messages.storeroom_was_deleted'));
    }

    public function products(Request $request, $storeroom)
    {
        ApiResponse::authorize($request->user()->can('manage', Storeroom::class));
        $storeroom = Storeroom::init()->findByIdOrFail($storeroom);
        return ApiResponse::message(trans('storeroom::messages.received_information_successfully'))
            ->addData('products',$storeroom->getProducts($request))
            ->send();
    }
}
