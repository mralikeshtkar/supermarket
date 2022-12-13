<?php

namespace Modules\Product\Http\Controllers\V1\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Feature\Entities\Feature;
use Modules\Feature\Entities\FeatureOption;
use Modules\Product\Entities\Product;
use Modules\Product\Transformers\V1\Api\Admin\AdminProductAttributeResource;
use Modules\Product\Transformers\V1\Api\Admin\AdminProductFeatureResource;
use Modules\Product\Transformers\V1\Api\Admin\AdminProductResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAdminProductAttributeController extends Controller
{
    /**
     * Show  product's attributes.
     *
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function index(Request $request, $product)
    {
        $product = Product::init()->selectColumns(['id', 'name'])->findOrFailById($product);
        $features = Feature::init()->productFeatures($product);
        try {
            return ApiResponse::message(trans('product::messages.received_information_successfully'))
                ->addData('product', new AdminProductResource($product))
                ->addData('features', AdminProductFeatureResource::collection($features))
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('product::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse|mixed
     */
    public function store(Request $request, $product)
    {
        ApiResponse::init($request->all(), [
            'features' => ['nullable', 'array:feature_id,attributes'],
            'features.feature_id' => ['exists:' . Feature::class . ',id'],
            'features.attributes' => ['nullable', 'array:option_id,value'],
            'features.attributes.option_id' => ['exists:' . FeatureOption::class . ',id'],
            'features.attributes.value' => ['string'],
        ])->validate();
        $product = Product::init()->findOrFailById($product);
        try {
            return DB::transaction(function () use ($request, $product) {
                $product->storeAttributes($request->get('attributes', []));
                return ApiResponse::message(trans("Registration information completed successfully"))
                    ->addData('features',AdminProductFeatureResource::collection(Feature::init()->productFeatures($product)))
                    ->send();
            });
        } catch (Throwable $e) {
            return ApiResponse::sendError(trans('product::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
