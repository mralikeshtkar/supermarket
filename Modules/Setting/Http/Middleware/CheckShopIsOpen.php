<?php

namespace Modules\Setting\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Setting\Entities\Setting;
use Symfony\Component\HttpFoundation\Response;

class CheckShopIsOpen
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Cache::get(Setting::SETTING_CACHE_KEY,collect())->get(Setting::SETTING_SHOP_IS_OPEN, true))
            return $next($request);
        return ApiResponse::sendError(trans("the_store_is_closed"), Response::HTTP_BAD_REQUEST);
    }
}
