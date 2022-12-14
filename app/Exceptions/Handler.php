<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Modules\Core\Responses\Api\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {

        });
    }

    public function render($request, Throwable $e)
    {
        /*if ($request->wantsJson() && $e instanceof AuthenticationException){
            return ApiResponse::message(trans('messages.please_log_in_to_your_account'))
                ->setCode(Response::HTTP_UNAUTHORIZED)
                ->hasError()
                ->send();
        }
        if ($request->wantsJson() && $e instanceof ModelNotFoundException){
            return ApiResponse::message(trans(":attribute not found",['attribute'=>trans(class_basename($e->getModel()))]))
                ->setCode(Response::HTTP_NOT_FOUND)
                ->hasError()
                ->send();
        }
        if ($request->wantsJson() && $e instanceof ThrottleRequestsException) {
            return ApiResponse::message(trans('messages.throttle'))
                ->setCode(Response::HTTP_TOO_MANY_REQUESTS)
                ->hasError()
                ->send();
        }
        if ($request->wantsJson() && $e instanceof NotFoundHttpException){
            return ApiResponse::message(trans('messages.not_found_http_exception'))
                ->setCode(Response::HTTP_NOT_FOUND)
                ->hasError()
                ->send();
        }*/
        return parent::render($request, $e); // TODO: Change the autogenerated stub
    }
}
