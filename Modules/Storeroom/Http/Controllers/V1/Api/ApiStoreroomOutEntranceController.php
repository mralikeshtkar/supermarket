<?php

namespace Modules\Storeroom\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Storeroom\Entities\Storeroom;
use Modules\Storeroom\Entities\StoreroomOutEntrance;
use Modules\Storeroom\Rules\StoreroomOutProductRule;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiStoreroomOutEntranceController extends Controller
{
    public function update(Request $request, $storeroom_out_entrance)
    {
        ApiResponse::authorize($request->user()->can('manage', Storeroom::class));
        ApiResponse::init($request->all(), [
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['required', new StoreroomOutProductRule()],
            'products.*.storeroom_entrance_id' => ['required', 'numeric'],
            'products.*.product_id' => ['required', 'numeric'],
            'products.*.quantity' => ['required', 'numeric'],
        ], [], [
            'products' => trans('Products'),
            'products.*' => trans('Product'),
            'products.*.storeroom_entrance_id' => trans('Storeroom entrance'),
            'products.*.product_id' => trans('Product id'),
            'products.*.quantity' => trans('Quantity'),
        ])->validate();
        try {
            $storeroom_out_entrance = StoreroomOutEntrance::init()->findByIdOrFail($storeroom_out_entrance);
            $storeroom_out_entrance = $storeroom_out_entrance->updateProducts($request);
            return ApiResponse::message(trans('storeroom::messages.storeroom_out_entrance_was_updated'))
                ->addData('storeroom_out_entrance', $storeroom_out_entrance)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('storeroom::messages.storeroom_out_entrance_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('storeroom::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }
}
