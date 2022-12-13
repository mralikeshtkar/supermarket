<?php

namespace Modules\Rack\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Rack\Entities\Rack;
use Modules\Rack\Entities\RackRow;
use Modules\Rack\Enums\RackRowStatus;
use Modules\Rack\Rules\ProductExistsInRackRowRule;
use Modules\Rack\Rules\ProductNotExistsInRackRowRule;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiRackRowController extends Controller
{
    /**
     * @param Request $request
     * @param $rack_row
     * @return JsonResponse
     */
    public function destroy(Request $request, $rack_row)
    {
        ApiResponse::authorize($request->user()->can('destroy', RackRow::class));
        try {
            $rack_row = RackRow::init()->findByIdOrFail($rack_row);
            $rack_row->delete();
            return ApiResponse::message(trans('rack::messages.rack_row_was_deleted'))->send();
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
