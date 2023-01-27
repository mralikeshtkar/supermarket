<?php

namespace Modules\Storeroom\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Storeroom\Entities\Storeroom;
use Modules\Storeroom\Entities\StoreroomEntrance;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiStoreroomEntranceController extends Controller
{
    public function update(Request $request, $storeroom_entrance)
    {
        ApiResponse::authorize($request->user()->can('manage', Storeroom::class));
        ApiResponse::init($request->all(), [
            'products' => ['required', 'array', 'min:1'],
            'products.*.id' => ['required','distinct', 'exists:products,id'],
            'products.*.quantity' => ['required', 'numeric', 'min:1'],
        ], [], [
            'products' => trans('Products'),
            'products.*.id' => trans('Product id'),
            'products.*.quantity' => trans('Product quantity'),
        ])->validate();
        try {
            $storeroom_entrance = StoreroomEntrance::init()->findByIdOrFail($storeroom_entrance);
            $storeroom_entrance = $storeroom_entrance->updateStoreroomEntrance($request);
            return ApiResponse::message(trans('storeroom::messages.storeroom_entrance_was_created'))
                ->addData('storeroom_entrance', $storeroom_entrance)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('storeroom::messages.storeroom_entrance_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('storeroom::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }
}
