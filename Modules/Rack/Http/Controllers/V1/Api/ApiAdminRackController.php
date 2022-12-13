<?php

namespace Modules\Rack\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Rack\Entities\Rack;
use Modules\Rack\Entities\RackRow;
use Modules\Rack\Enums\RackStatus;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAdminRackController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return ApiResponse::message(trans('rack::messages.received_information_successfully'))
            ->addData('racks', Rack::init()->getAdminIndex($request))
            ->send();
    }

    /**
     * @param $rack
     * @return JsonResponse
     */
    public function show($rack)
    {
        try {
            $rack = Rack::init()->findByIdOrFail($rack, ['rows']);
            return ApiResponse::message(trans('rack::messages.received_information_successfully'))
                ->addData('rack', $rack)
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
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::authorize($request->user()->can('store', Rack::class));
        ApiResponse::init($request->all(), [
            'title' => ['required', 'string', 'unique:' . Rack::class . ',title'],
            'url' => ['nullable', 'url'],
            'description' => ['nullable', 'string'],
        ])->validate();
        try {
            Rack::init()->store($request);
            return ApiResponse::message(trans('rack::messages.rack_was_created'))
                ->addData('racks', Rack::init()->getAdminIndex($request))
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('rack::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $rack
     * @return JsonResponse
     */
    public function update(Request $request, $rack)
    {
        ApiResponse::authorize($request->user()->can('update', Rack::class));
        ApiResponse::init($request->all(), [
            'title' => ['required', 'string', 'unique:' . Rack::class . ',title,' . $rack],
            'url' => ['nullable', 'url'],
            'description' => ['nullable', 'string'],
        ])->validate();
        try {
            $rack = Rack::init()->findByIdOrFail($rack);
            $rack->updateRack($request);
            return ApiResponse::message(trans('rack::messages.rack_was_updated'))
                ->addData('racks', Rack::init()->getAdminIndex($request))
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
     * @return JsonResponse
     */
    public function changeSort(Request $request)
    {
        ApiResponse::init($request->all(), [
            'rack_ids' => ['required', 'array'],
            'rack_ids.*' => ['exists:' . Rack::class . ',id'],
        ])->validate();
        try {
            Rack::init()->changeSort($request->rack_ids);
            return ApiResponse::message(trans('rack::messages.received_information_successfully'))->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('rack::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $rack
     * @return JsonResponse
     */
    public function changeSortRows(Request $request, $rack)
    {
        ApiResponse::init($request->all(), [
            'rack_row_ids' => ['required', 'array'],
            'rack_row_ids.*' => [Rule::exists(RackRow::class, 'id')->where('rack_id', $rack)],
        ])->validate();
        try {
            $rack = Rack::init()->findByIdOrFail($rack, ['rows']);
            $rack->changeSortRows($request->rack_row_ids);
            return ApiResponse::message(trans('rack::messages.rack_row_priority_was_updated'))
                ->addData('rack', $rack->refresh())
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('rack::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $rack
     * @return JsonResponse
     */
    public function accept(Request $request, $rack)
    {
        return $this->_changeStatus($rack, RackStatus::Accepted);
    }

    /**
     * @param Request $request
     * @param $rack
     * @return JsonResponse
     */
    public function reject(Request $request, $rack)
    {
        return $this->_changeStatus($rack, RackStatus::Rejected);
    }

    public function destroy(Request $request, $rack)
    {
        try {
            $rack = Rack::init()->findByIdOrFail($rack);
            $rack->destroyRack();
            return ApiResponse::message(trans('rack::messages.rack_was_deleted'))
                ->addData('racks', Rack::init()->getAdminIndex($request))
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
     * @param $rack
     * @param $status
     * @return JsonResponse
     */
    private function _changeStatus($rack, $status): JsonResponse
    {
        try {
            $rack = Rack::init()->findByIdOrFail($rack);
            $rack = $rack->changeStatus($status);
            return ApiResponse::message(trans('rack::messages.rack_status_was_changed'))
                ->addData('rack', $rack)
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
}
