<?php

namespace Modules\Product\Http\Controllers\V1\Api\Admin;

use App\Http\Controllers\Controller;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\Product\Entities\Faq;
use Modules\Product\Entities\Product;
use Modules\Product\Enums\FaqStatus;
use Modules\Product\Transformers\Api\Admin\ApiAdminFaqResource;

class ApiAdminFaqController extends Controller
{
    /**
     * @param Request $request
     * @param $product
     * @param $faq
     * @return JsonResponse
     */
    public function index(Request $request, $product, $faq = null)
    {
        $product = Product::init()->selectColumns(['id'])->withScopes(['accepted'])->findOrFailById($product);
        if ($faq) $faq = Faq::init()->selectColumns(['id'])->withScopes(['parent'])->findOrFailById($faq);
        $faqs = Faq::init()->selectColumns(['id', 'user_id', 'product_id', 'body', 'status', 'created_at'])
            ->paginateAdmin($request, $product, $faq);
        $resource = ApiPaginationResource::make($faqs)->additional(['itemsResource' => ApiAdminFaqResource::class]);
        return ApiResponse::message(trans("The operation was done successfully"))
            ->addData('faqs', $resource)
            ->send();
    }

    /**
     * @param Request $request
     * @param $faq
     * @return JsonResponse
     */
    public function show(Request $request, $faq)
    {
        /** @var Faq $faq */
        $faq = Faq::init()->selectColumns(['id', 'user_id', 'product_id', 'body', 'status', 'created_at'])
            ->findOrFailById($faq);
        return ApiResponse::message(trans("The operation was done successfully"))
            ->addData('faq', new ApiAdminFaqResource($faq))
            ->send();
    }

    /**
     * @param Request $request
     * @param $faq
     * @return JsonResponse
     */
    public function update(Request $request, $faq)
    {
        /** @var Faq $faq */
        $faq = Faq::init()->selectColumns(['id'])->findOrFailById($faq);
        ApiResponse::init($request->all(), [
            'body' => ['required', 'string'],
            'status' => ['required', new EnumValue(FaqStatus::class)],
        ])->validate();
        $faq->updateRow($request);
        return ApiResponse::message(trans("The operation was done successfully"))->send();
    }

    /**
     * @param Request $request
     * @param $faq
     * @return JsonResponse
     */
    public function destroy(Request $request, $faq)
    {
        /** @var Faq $faq */
        $faq = Faq::init()->selectColumns(['id'])->findOrFailById($faq);
        $faq->destroyRow();
        return ApiResponse::message(trans("The operation was done successfully"))->send();
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
            ->getAdminRepliesPaginate($request);
        $resource = ApiPaginationResource::make($replies)->additional(['itemsResource' => ApiAdminFaqResource::class]);
        return ApiResponse::message(trans("The operation was done successfully"))
            ->addData('replies', $resource)
            ->send();
    }
}
