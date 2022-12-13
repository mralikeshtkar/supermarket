<?php

namespace Modules\Setting\Http\Controllers\V1\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Setting\Entities\Setting;
use Modules\Setting\Transformers\V1\Admin\AdminSettingResource;

class ApiAdminSettingController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return ApiResponse::message(trans("Received information successfully"))
            ->addData('settings', AdminSettingResource::make(Cache::get(Setting::SETTING_CACHE_KEY,collect())))
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::init($request->all(), Setting::SETTING_RULES)->validate();
        Setting::init()->store($request);
        return ApiResponse::message(trans("Registration information completed successfully"))
            ->addData('settings', AdminSettingResource::make(Cache::get(Setting::SETTING_CACHE_KEY,collect())))
            ->send();
    }
}
