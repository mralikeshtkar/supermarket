<?php

namespace Modules\Rack\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Product\Entities\Product;
use Modules\Rack\Entities\Rack;
use Modules\Rack\Entities\RackRow;
use Modules\Rack\Enums\RackRowStatus;
use Modules\Rack\Rules\ProductExistsInRackRowRule;
use Modules\Rack\Rules\ProductNotExistsInRackRowRule;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAdminRackRowController extends Controller
{
    /**
     * @param Request $request
     * @param $rack
     * @return JsonResponse
     */
    public function store(Request $request, $rack)
    {
        ApiResponse::authorize($request->user()->can('manageRackRows', Rack::class));
        ApiResponse::init($request->all(), [
            'title' => ['required', 'string'],
            'number_limit' => ['required', 'numeric', 'min:0'],
        ], [], [
            'number_limit' => trans('Number limit product'),
        ])->validate();
        try {
            $rack = Rack::init()->findByIdOrFail($rack, ['rows']);
            RackRow::init()->store($rack, $request);
            return ApiResponse::message(trans('rack::messages.rack_row_was_created'))
                ->addData('rack', $rack->refresh())
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('rack::messages.rack_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('rack::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $rack_row
     * @return JsonResponse
     */
    public function show(Request $request,$rack_row)
    {
        ApiResponse::authorize($request->user()->can('manageRackRows', Rack::class));
        try {
            $rack_row = RackRow::init()->findByIdOrFail($rack_row, ['products', 'products.image']);
            return ApiResponse::message(trans('rack::messages.received_information_successfully'))
                ->addData('rack_row', $rack_row)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('rack::messages.rack_row_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('rack::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function products(Request $request)
    {
        return ApiResponse::message(trans('rack::messages.received_information_successfully'))
            ->addData('products', Product::init()->getRackRowProducts($request))
            ->send();
    }

    /**
     * @param Request $request
     * @param $rack_row
     * @return JsonResponse
     */
    public function update(Request $request, $rack_row)
    {
        ApiResponse::authorize($request->user()->can('manageRackRows', Rack::class));
        ApiResponse::init($request->all(), [
            'title' => ['required', 'string'],
            'number_limit' => ['required', 'numeric', 'min:0'],
            'rack_id' => ['required', 'exists:' . Rack::class . ',id'],
        ])->validate();
        try {
            $rack_row = RackRow::init()->findByIdOrFail($rack_row);
            $rack_row->updateRackRow($request);
            return ApiResponse::message(trans('rack::messages.rack_row_was_updated'))
                ->addData('rack', Rack::init()->findByIdOrFail($request->rack_id, ['rows']))
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('rack::messages.rack_row_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('rack::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $rack_row
     * @return JsonResponse
     */
    public function destroy(Request $request, $rack_row)
    {
        ApiResponse::authorize($request->user()->can('manageRackRows', Rack::class));
        ApiResponse::init($request->all(), [
            'rack_id' => ['required', 'exists:' . Rack::class . ',id'],
        ])->validate();
        try {
            $rack_row = RackRow::init()->findByIdOrFail($rack_row);
            $rack_row->destroyRackRow();
            return ApiResponse::message(trans('rack::messages.rack_row_was_deleted'))
                ->addData('rack', Rack::init()->findByIdOrFail($request->rack_id, ['rows']))
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('rack::messages.rack_row_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('rack::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $rack_row
     * @return JsonResponse
     */
    public function attach(Request $request, $rack_row)
    {
        ApiResponse::authorize($request->user()->can('manageRackRows', Rack::class));
        ApiResponse::init($request->all(), [
            'product_id' => ['required', /*new ProductNotExistsInRackRowRule($rack_row)*/]
        ], [], [
            'product_id' => trans('Product'),
        ])->validate();
        try {
            $rack_row = RackRow::init()->findByIdOrFail($rack_row);
            $rack_row = $rack_row->attachProduct($request);
            return ApiResponse::message(trans('rack::messages.product_added_to_rack_row'))
                ->addData('rack_row', $rack_row)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('rack::messages.rack_row_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('rack::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $rack_row
     * @return JsonResponse
     */
    public function detach(Request $request, $rack_row)
    {
        ApiResponse::authorize($request->user()->can('manageRackRows', Rack::class));
        ApiResponse::init($request->all(), [
            'product_id' => ['required', /*new ProductExistsInRackRowRule($rack_row)*/]
        ], [], [
            'product_id' => trans('Product'),
        ])->validate();
        try {
            $rack_row = RackRow::init()->findByIdOrFail($rack_row);
            $rack_row->detachProduct($request->product_id);
            return ApiResponse::message(trans('rack::messages.product_deleted_from_rack_row'))
                ->addData('rack_row', $rack_row)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('rack::messages.rack_row_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('rack::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $rack_row
     * @return JsonResponse
     */
    public function active(Request $request, $rack_row)
    {
        ApiResponse::authorize($request->user()->can('manageRackRows', Rack::class));
        return $this->_changeStatus($rack_row, RackRowStatus::Active);
    }

    /**
     * @param Request $request
     * @param $rack_row
     * @return JsonResponse
     */
    public function inactive(Request $request, $rack_row)
    {
        ApiResponse::authorize($request->user()->can('manageRackRows', Rack::class));
        return $this->_changeStatus($rack_row, RackRowStatus::Inactive);
    }

    /**
     * @param Request $request
     * @param $rack_row
     * @return JsonResponse
     */
    public function changeSort(Request $request, $rack_row)
    {
        /*ApiResponse::init($request->all(), [
            'sort' => ['required', 'array', 'min:1'],
            'sort.*.product_id' => ['required',],
            'sort.*.id' => ['required', 'exists:product_rack_row,id'],
        ], [], [
            'sort.product_id' => trans('Product'),
            'sort.product_id.*' => trans('Product'),
        ])->validate();*/
        try {
            $rack_row = RackRow::init()->findByIdOrFail($rack_row);
            $rack_row->changeSortProducts($request->sort);
            return ApiResponse::message(trans('rack::messages.rack_row_products_sort_has_changed'))
                ->addData('rack_row', $rack_row->load('products', 'products.image'))
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('rack::messages.rack_row_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('rack::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param $rack_row
     * @param $status
     * @return JsonResponse
     */
    private function _changeStatus($rack_row, $status): JsonResponse
    {
        try {
            $rack_row = RackRow::init()->findByIdOrFail($rack_row);
            $rack_row = $rack_row->updateStatus($status);
            return ApiResponse::message(trans('rack::messages.rack_row_status_was_updated'))
                ->addData('rack_row', $rack_row)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('rack::messages.rack_row_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('rack::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        }
    }
}
