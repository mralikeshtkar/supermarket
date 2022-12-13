<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class Cart
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
        if (is_null($request->user()->cart) && $request->wantsJson())
            return ApiResponse::sendError(trans('user::messages.your_cart_is_empty'), Response::HTTP_BAD_REQUEST);
        return $next($request);
    }
}
