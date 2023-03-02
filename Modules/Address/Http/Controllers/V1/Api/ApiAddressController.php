<?php

namespace Modules\Address\Http\Controllers\V1\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Modules\Address\Entities\Address;
use Modules\Address\Entities\City;
use Modules\Address\Entities\District;
use Modules\Address\Entities\Province;
use Modules\Address\Rules\PostalCodeRule;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Rules\MobileRule;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Throwable;

class ApiAddressController extends Controller
{
    public function addresses(Request $request)
    {
        try {
            $address = Address::init()->getUserAddresses($request);
            return ApiResponse::message(trans('address::messages.address_was_created'))
                ->addData('address', $address)
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('address::messages.internal_error'), HttpResponse::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Store an address from call api.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::init($request->all(), [
            'province_id' => ['required', 'exists:' . Province::class . ',id'],
            'city_id' => ['required', 'exists:' . City::class . ',id'],
            'district_id' => ['nullable', Rule::exists(District::class, 'id')->where('city_id', $request->city_id)],
            'name' => ['required', 'string'],
            'address' => ['required', 'string'],
            'postal_code' => ['required', new PostalCodeRule()],
            'mobile' => ['required', new MobileRule()],
            'latitude' => 'nullable|between:-90,90',
            'longitude' => 'nullable|between:-180,180'
        ], [], trans('address::validation.attributes.' . Address::class))->validate();
        try {
            $address = Address::init()->store($request);
            return ApiResponse::message(trans('address::messages.address_was_created'))
                ->addData('address', $address)
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('address::messages.internal_error'), HttpResponse::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Delete an address from call api.
     *
     * @param Request $request
     * @param $address
     * @return JsonResponse
     */
    public function destroy(Request $request, $address)
    {
        try {
            $address = Address::init()->findByColumnOrFail($address);
            ApiResponse::authorize($request->user()->can('destroy', $address));
            $address->destroyAddress();
            return ApiResponse::message(trans('address::messages.address_was_deleted'))
                ->send();
        } catch (HttpResponseException $e) {
            return ApiResponse::message(trans('core::messages.access_forbidden'))
                ->hasError()
                ->setCode(HttpResponse::HTTP_FORBIDDEN)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('address::messages.address_not_found'), HttpResponse::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('feature::messages.internal_error'), HttpResponse::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Find a valid address and update it's with request data.
     *
     * @param Request $request
     * @param $address
     * @return JsonResponse
     */
    public function update(Request $request, $address)
    {
        ApiResponse::init($request->all(), [
            'province_id' => ['required', 'exists:' . Province::class . ',id'],
            'city_id' => ['required', 'exists:' . City::class . ',id'],
            'district_id' => ['nullable', Rule::exists(District::class, 'id')->where('city_id', $request->city_id)],
            'name' => ['required', 'string'],
            'address' => ['required', 'string'],
            'postal_code' => ['required', new PostalCodeRule()],
            'mobile' => ['required', new MobileRule()],
            'latitude' => 'nullable|between:-90,90',
            'longitude' => 'nullable|between:-180,180'
        ], [], trans('address::validation.attributes.' . Province::class))->validate();
        try {
            $address = Address::init()->findByColumnOrFail($address);
            ApiResponse::authorize($request->user()->can('update', $address));
            $address = $address->updateAddress($request);
            return ApiResponse::message(trans('address::messages.address_was_updated'))
                ->addData('address', $address)
                ->send();
        } catch (HttpResponseException $e) {
            return ApiResponse::message(trans('core::messages.access_forbidden'))
                ->hasError()
                ->setCode(HttpResponse::HTTP_FORBIDDEN)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('address::messages.address_not_found'), HttpResponse::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('feature::messages.internal_error'), HttpResponse::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Find a with its city, province and user.
     *
     * @param Request $request
     * @param $address
     * @return JsonResponse
     */
    public function show(Request $request, $address)
    {
        try {
            $address = Address::init()->findByColumnOrFail($address);
            ApiResponse::authorize($request->user()->can('show', $address));
            $address->load(['user', 'city', 'province']);
            return ApiResponse::message(trans('address::messages.received_information_successfully'))
                ->addData('address', $address)
                ->send();
        } catch (HttpResponseException $e) {
            return ApiResponse::message(trans('core::messages.access_forbidden'))
                ->hasError()
                ->setCode(HttpResponse::HTTP_FORBIDDEN)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('address::messages.address_not_found'), HttpResponse::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('feature::messages.internal_error'), HttpResponse::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }
}
