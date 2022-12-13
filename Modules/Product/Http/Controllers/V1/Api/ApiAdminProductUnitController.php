<?php

namespace Modules\Product\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use BenSampo\Enum\Rules\EnumKey;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Product\Entities\ProductUnit;
use Modules\Product\Enums\ProductUnitStatus;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAdminProductUnitController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('productUnits', ProductUnit::init()->getAdminIndexPaginate($request))
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function productUnitAccepted(Request $request)
    {
        return ApiResponse::message(trans('product::messages.received_information_successfully'))
            ->addData('productUnits', ProductUnit::init()->onlyAccepted())
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::init($request->all(), [
            'title' => ['required', 'string', 'unique:product_units,title'],
            'status' => ['nullable', new EnumValue(ProductUnitStatus::class)],
        ])->validate();
        try {
            ProductUnit::init()->store($request);
            return ApiResponse::sendMessage(trans('product::messages.product_unit_was_created'));
        } catch (Throwable $e) {
            return ApiResponse::message(trans('product::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->hasError()
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $productUnit
     * @return JsonResponse
     */
    public function update(Request $request, $productUnit)
    {
        try {
            $productUnit = ProductUnit::init()->findOrFailById($productUnit);
            ApiResponse::init($request->all(), [
                'title' => ['required', 'string', 'unique:product_units,title,' . $productUnit->id],
                'status' => ['nullable', new EnumValue(ProductUnitStatus::class)],
            ])->validate();
            $productUnit->updateUnit($request);
            return ApiResponse::sendMessage(trans('product::messages.product_unit_was_updated'));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::message(trans('product::messages.product_unit_not_found'), Response::HTTP_NOT_FOUND)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        } catch (HttpResponseException $e) {
            return $e->getResponse();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('product::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->hasError()
                ->send();
        }
    }

    /**
     * @param $productUnit
     * @return JsonResponse
     */
    public function destroy($productUnit)
    {
        try {
            $unit = ProductUnit::init()->findOrFailById($productUnit);
            $unit->destroyUnit();
            return ApiResponse::message(trans('product::messages.product_was_deleted'))
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::message(trans('product::messages.product_unit_not_found'), Response::HTTP_NOT_FOUND)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('product::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->hasError()
                ->send();
        }
    }

    /**
     * @param $productUnit
     * @return JsonResponse
     */
    public function accept($productUnit)
    {
        return $this->_changeStatus($productUnit, ProductUnitStatus::Accepted);
    }

    /**
     * @param $productUnit
     * @return JsonResponse
     */
    public function reject($productUnit)
    {
        return $this->_changeStatus($productUnit, ProductUnitStatus::Rejected);
    }

    /**
     * @param $productUnit
     * @param $status
     * @return JsonResponse
     */
    private function _changeStatus($productUnit, $status)
    {
        try {
            $productUnit = ProductUnit::init()->findOrFailById($productUnit);
            $productUnit = $productUnit->changeStatus($status);
            return ApiResponse::message(trans('product::messages.product_unit_status_was_updated'))
                ->addData('productUnit', $productUnit)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::message(trans('product::messages.product_unit_not_found'), Response::HTTP_NOT_FOUND)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('product::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->hasError()
                ->send();
        }
    }
}
