<?php

namespace Modules\Discount\Http\Controllers\V1\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Discount\Entities\Discount;
use Modules\Discount\Rules\DiscountableIdRule;
use Modules\Discount\Rules\DiscountableTypeRule;
use Modules\Discount\Rules\DiscountCodeRule;
use Modules\Discount\Transformers\V1\Api\Admin\AdminDiscountResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAdminDiscountController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $discounts = Discount::init()->getAdminIndexPaginate($request);
        return ApiResponse::message(trans("discount::messages.received_information_successfully"))
            ->addData('discounts', ApiPaginationResource::make($discounts)->additional(['itemsResource' => AdminDiscountResource::class]))
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
//        ApiResponse::authorize($request->user()->can('store', Discount::class));
        $request->merge([
            'discountables' => [
                'discountable_type' => Relation::getMorphedModel(optional($request->discountables)->offsetGet('discountable_type')),
                'discountable_ids' => optional($request->discountables)->offsetGet('discountable_ids'),
            ],
        ]);
        ApiResponse::init($request->all(), $this->_validationRules($request), [], trans('discount::validation.attributes'))->validate();
        try {
            Discount::init()->store($request);
            return ApiResponse::message(trans('discount::messages.discount_was_created'))->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('discount::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $discount
     * @return JsonResponse
     */
    public function update(Request $request, $discount)
    {
//        ApiResponse::authorize($request->user()->can('update', Discount::class));
        $request->merge([
            'discountables' => [
                'discountable_type' => Relation::getMorphedModel(optional($request->discountables)->offsetGet('discountable_type')),
                'discountable_ids' => optional($request->discountables)->offsetGet('discountable_ids'),
            ],
        ]);
        $discount = Discount::init()->findOrFailById($discount);
        ApiResponse::init($request->all(), $this->_validationRules($request, $discount->id), [], trans('discount::validation.attributes'))->validate();
        try {
            $discount->updateDiscount($request);
            return ApiResponse::message(trans('discount::messages.discount_was_updated'))->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('discount::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $discount
     * @return JsonResponse|mixed
     */
    public function destroy(Request $request, $discount)
    {
        ApiResponse::authorize($request->user()->can('destroy', Discount::class));
        $discount = Discount::init()->selectColumns(['id'])->findOrFailById($discount);
        try {
            return DB::transaction(function () use ($discount) {
                $discount->destroyDiscount();
                return ApiResponse::message(trans('discount::messages.discount_was_deleted'))
                    ->send();
            });
        } catch (Throwable $e) {
            return ApiResponse::message(trans('discount::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $discount
     * @return JsonResponse
     */
    public function changeStatus(Request $request, $discount)
    {
        $discount = Discount::init()->selectColumns(['id', 'status'])->findOrFailById($discount);
        $discount = $discount->changeStatus($discount);
        return ApiResponse::message(trans("Registration information completed successfully"))
            ->addData('discount', new AdminDiscountResource($discount))
            ->send();
    }

    /**
     * @param Request $request
     * @param $discount
     * @return array
     */
    private function _validationRules(Request $request, $discount = null): array
    {
        return [
            'code' => ['nullable', new DiscountCodeRule($discount)],
            'amount' => ['required', 'numeric', 'min:1'],
            'is_percent' => ['nullable', 'boolean'],
            'start_at' => ['nullable', 'jdatetime:Y/n/j H:i'],
            'expire_at' => ['nullable', 'jdatetime:Y/n/j H:i', 'jdatetime_after:' . $request->start_at . ',Y/n/j H:i'],
            'usage_limitation' => ['nullable', 'numeric', 'min:1'],
            'description' => ['nullable', 'string'],
            'discountables' => ['required', 'array'],
            'discountables.discountable_type' => ['nullable', new DiscountableTypeRule()],
            'discountables.discountable_ids' => ['nullable', new DiscountableIdRule($request)],
        ];
    }

}
