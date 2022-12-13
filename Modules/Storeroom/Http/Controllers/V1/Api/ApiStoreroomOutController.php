<?php

namespace Modules\Storeroom\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Storeroom\Entities\StoreroomOut;
use Modules\Storeroom\Rules\StoreroomOutProductRule;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiStoreroomOutController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|mixed
     */
    public function store(Request $request)
    {
        ApiResponse::init($request->all(), [
            'products' => ['required', 'array', 'min:1'],
            'products.*.storeroom_entrance_id' => ['required', 'numeric'],
            'products.*.product_id' => ['required', 'numeric'],
            'products.*.quantity' => ['required', 'numeric'],
            'products.*' => ['required', new StoreroomOutProductRule()],
        ], [], [
            'products' => trans('Products'),
            'products.*' => trans('Product'),
            'products.*.storeroom_entrance_id' => trans('Storeroom entrance'),
            'products.*.product_id' => trans('Product id'),
            'products.*.quantity' => trans('Quantity'),
        ])->validate();
        try {
            return DB::transaction(function () use ($request) {
                $storeroom_out = StoreroomOut::init()->store($request);
                foreach ($request->get('products', []) as $product) {
                    $storeroom_out_entrance = $storeroom_out->storeroomOutEntrances()->firstOrCreate([
                        'storeroom_entrance_id' => $product['storeroom_entrance_id'],
                    ], [
                        'storeroom_entrance_id' => $product['storeroom_entrance_id'],
                    ]);
                    $storeroom_out_entrance->products()->attach(collect($request->get('products', []))
                        ->where('storeroom_entrance_id', $product['storeroom_entrance_id'])
                        ->mapWithKeys(fn($item, $key) => [
                            $item['product_id'] => [
                                'quantity' => $item['quantity'],
                            ],
                        ]));
                }
                return ApiResponse::message(trans('storeroom::messages.received_information_successfully'))
                    ->addData('storeroom_out', $storeroom_out->load(
                        'storeroomOutEntrances',
                        'storeroomOutEntrances.storeroomEntrance',
                        'storeroomOutEntrances.products',
                        'storeroomOutEntrances.products.gallery',
                        'storeroomOutEntrances.products.media'
                    ))->send();
            });
        } catch (Throwable $e) {
            return ApiResponse::message(trans('storeroom::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }
}
