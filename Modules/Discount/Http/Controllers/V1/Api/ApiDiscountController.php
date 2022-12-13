<?php

namespace Modules\Discount\Http\Controllers\V1\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Discount\Entities\Discount;
use Modules\Discount\Rules\DiscountableIdRule;
use Modules\Discount\Rules\DiscountableTypeRule;
use Modules\Discount\Rules\DiscountCodeRule;
use Modules\Product\Entities\Product;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiDiscountController extends Controller
{
    /**
     * Store a discount from call api.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::authorize($request->user()->can('store', Discount::class));
        $request->merge([
            'discountables' => [
                'discountable_type' => Relation::getMorphedModel(optional($request->discountables)->offsetGet('discountable_type')),
                'discountable_ids' => optional($request->discountables)->offsetGet('discountable_ids'),
            ],
            'start_at' => $request->filled('start_at') ? $request->start_at : now()->format('Y/m/d H:i:s'),
        ]);
        ApiResponse::init($request->all(), [
            'code' => ['nullable', new DiscountCodeRule()],
            'amount' => ['required', 'numeric', 'min:1'],
            'is_percent' => ['nullable', 'boolean'],
            'start_at' => ['nullable', 'jdatetime:Y/m/d H:i:s'],
            'expire_at' => ['nullable', 'jdatetime:Y/m/d H:i:s', 'jdatetime_after:' . $request->start_at . ',Y/m/d H:i:s'],
            'usage_limitation' => ['nullable', 'numeric', 'min:1'],
            'description' => ['nullable', 'string'],
            'discountables' => ['required', 'array'],
            'discountables.discountable_type' => ['nullable', new DiscountableTypeRule()],
            'discountables.discountable_ids' => ['nullable', new DiscountableIdRule($request)],
            'priority' => ['nullable', 'numeric', 'min:0'],
        ], [], trans('discount::validation.attributes'))->validate();
        try {
            $discount = Discount::init()->store($request);
            return ApiResponse::message(trans('discount::messages.discount_was_created'))
                ->addData('discount', $discount)
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('discount::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Store a discount from call api.
     *
     * @param Request $request
     * @param $discount
     * @return JsonResponse
     */
    public function update(Request $request, $discount)
    {
        ApiResponse::authorize($request->user()->can('update', Discount::class));
        $request->merge([
            'start_at' => $request->filled('start_at') ? $request->start_at : now()->format('Y/m/d H:i:s'),
            'discountables' => [
                'discountable_type' => Relation::getMorphedModel(optional($request->discountables)->offsetGet('discountable_type')),
                'discountable_ids' => optional($request->discountables)->offsetGet('discountable_ids'),
            ],
        ]);
        ApiResponse::init($request->all(), [
            'code' => ['nullable', new DiscountCodeRule()],
            'amount' => ['required', 'numeric', 'min:1'],
            'is_percent' => ['nullable', 'boolean'],
            'start_at' => ['nullable', 'jdatetime:Y/m/d H:i:s'],
            'expire_at' => ['nullable', 'jdatetime:Y/m/d H:i:s', 'jdatetime_after:' . $request->start_at . ',Y/m/d H:i:s'],
            'usage_limitation' => ['nullable', 'numeric', 'min:1'],
            'description' => ['nullable', 'string'],
            'discountables' => ['required', 'array'],
            'discountables.discountable_type' => ['nullable', new DiscountableTypeRule()],
            'discountables.discountable_ids' => ['nullable', new DiscountableIdRule($request)],
            'priority' => ['nullable', 'numeric', 'min:0'],
        ], [], trans('discount::validation.attributes'))->validate();
        try {
            $discount = Discount::init()->findByColumnOrFail($discount);
            $discount = $discount->updateDiscount($request);
            return ApiResponse::message(trans('discount::messages.discount_was_updated'))
                ->addData('discount', $discount)
                ->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('discount::messages.discount_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('discount::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Destroy a discount from call api.
     *
     * @param Request $request
     * @param $discount
     * @return JsonResponse
     */
    public function destroy(Request $request, $discount)
    {
        ApiResponse::authorize($request->user()->can('destroy', Discount::class));
        try {
            $discount = Discount::init()->findByColumnOrFail($discount);
            return DB::transaction(function () use ($discount) {
                $discount->destroyDiscount();
                return ApiResponse::message(trans('discount::messages.discount_was_deleted'))
                    ->send();
            });
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendMessage(trans('discount::messages.discount_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('discount::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }
}
