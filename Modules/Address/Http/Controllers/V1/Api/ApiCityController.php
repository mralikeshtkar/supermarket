<?php

namespace Modules\Address\Http\Controllers\V1\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Address\Entities\City;
use Modules\Address\Entities\Province;
use Modules\Address\Transformers\Api\ApiDistrictResource;
use Modules\Core\Responses\Api\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiCityController extends Controller
{
    /**
     * Store a city from call api.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::authorize($request->user()->can('store', City::class));
        ApiResponse::init($request->all(), [
            'province_id' => ['required', 'exists:' . Province::class . ',id'],
            'name' => ['required', 'string', 'unique:' . City::class . ',name'],
        ], [], trans('address::validation.attributes.' . City::class));
        try {
            $city = City::init()->store($request);
            return ApiResponse::message(trans('address::messages.city_was_created'))
                ->addData('city', $city)
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('address::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Find a valid city and update it's with request data.
     *
     * @param Request $request
     * @param $province
     * @return JsonResponse
     */
    public function update(Request $request, $city)
    {
        ApiResponse::authorize($request->user()->can('update', City::class));
        ApiResponse::init($request->all(), [
            'province_id' => ['required', 'exists:' . Province::class . ',id'],
            'name' => ['required', 'string', 'unique:' . City::class . ',name,' . $city],
        ], [], trans('address::validation.attributes.' . City::class))->validate();
        try {
            $city = City::init()->findByColumnOrFail($city);
            $city = $city->updateCity($request);
            return ApiResponse::message(trans('address::messages.city_was_updated'))
                ->addData('city', $city)
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
     * Find a city with its cities.
     *
     * @param Request $request
     * @param $city
     * @return JsonResponse
     */
    public function show(Request $request, $city)
    {
        try {
            $city = City::init()->findByColumnOrFail($city);
            $city->load('province');
            return ApiResponse::message(trans('address::messages.received_information_successfully'))
                ->addData('city', $city)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('address::messages.province_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('feature::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    public function districts(Request $request, $city)
    {
        $city = City::init()->select(['id'])->findByColumnOrFail($city);
        $districts = $city->districts()
            ->select(['id', 'city_id', 'name', 'price'])
            ->get();
        return ApiResponse::message(trans('address::messages.received_information_successfully'))
            ->addData('districts', ApiDistrictResource::collection($districts))
            ->send();
    }

    /**
     * Delete a city from call api.
     *
     * @param Request $request
     * @param $city
     * @return JsonResponse
     */
    public function destroy(Request $request, $city)
    {
        ApiResponse::authorize($request->user()->can('destroy', City::class));
        try {
            $city = City::init()->findByColumnOrFail($city);
            $city->destroyCity();
            return ApiResponse::message(trans('address::messages.city_was_deleted'))
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('address::messages.city_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('feature::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }
}
