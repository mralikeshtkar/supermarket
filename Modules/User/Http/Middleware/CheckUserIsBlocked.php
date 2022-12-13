<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIsBlocked
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
        if ($request->user() && $request->user()->is_blocked) return ApiResponse::sendError(trans('user::messages.the_user_is_blocked'), Response::HTTP_BAD_REQUEST);
        return $next($request);
    }
}
