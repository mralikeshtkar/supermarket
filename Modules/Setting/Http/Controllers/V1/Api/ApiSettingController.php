<?php

namespace Modules\Setting\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Setting\Entities\Setting;
use OpenApi\Annotations as OA;

class ApiSettingController extends Controller
{
    /**
     *
     * @OA\Get (
     *     path="/check-store-is-open",
     *     summary="آیا فروشگاه باز است یا بسته",
     *     description="",
     *     tags={"تنظیمات"},
     *     @OA\Response(
     *         response=200,
     *         description="عملیات موفق",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function checkStoreIsOpen(Request $request)
    {
        return ApiResponse::message(trans("Received information successfully"))
            ->addData('is_open', boolval(Cache::get(Setting::SETTING_CACHE_KEY,collect(),collect())->get(Setting::SETTING_SHOP_IS_OPEN, true)))
            ->send();
    }
}
