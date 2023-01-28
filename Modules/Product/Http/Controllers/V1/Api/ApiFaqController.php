<?php

namespace Modules\Product\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Product\Entities\Faq;
use Modules\Product\Entities\Product;
use Modules\Product\Transformers\Api\ApiFaqResource;
use Symfony\Component\HttpFoundation\Response;

class ApiFaqController extends Controller
{
    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function index(Request $request, $product)
    {
        $product = Product::init()->selectColumns(['id'])->withScopes(['accepted'])->findOrFailById($product);
        $faqs = Faq::init()->selectColumns(['id', 'user_id', 'product_id', 'body', 'created_at'])
            ->withRelationships(['user:id,name,email'])
            ->withScopes(['accepted'])
            ->getIndexPaginate($request, $product);
        $resource = ApiPaginationResource::make($faqs)->additional(['itemsResource' => ApiFaqResource::class]);
        return ApiResponse::message(trans("The operation was done successfully"))
            ->addData('faqs', $resource)
            ->send();
    }

    /**
     * @param Request $request
     * @param $faq
     * @return JsonResponse
     */
    public function replies(Request $request, $faq)
    {
        /** @var Faq $faq */
        $faq = Faq::init()->selectColumns(['id'])->findOrFailById($faq);
        $replies = $faq->selectColumns(['id','user_id','parent_id','body','created_at'])
            ->withRelationships(['user:id,name,email'])
            ->getRepliesPaginate($request);
        $resource = ApiPaginationResource::make($replies)->additional(['itemsResource' => ApiFaqResource::class]);
        return ApiResponse::message(trans("The operation was done successfully"))
            ->addData('replies', $resource)
            ->send();
    }

    /**
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function store(Request $request, $product)
    {
        $product = Product::init()->selectColumns(['id'])->withScopes(['accepted'])->findOrFailById($product);
        ApiResponse::init($request->all(), [
            'parent_id' => ['nullable', Rule::exists(Faq::class, 'id')->whereNull('parent_id')],
            'body' => ['required', 'string'],
        ])->validate();
        Faq::init()->store($request, $product);
        return ApiResponse::message(trans("The operation was done successfully"), Response::HTTP_CREATED)->send();
    }
}
