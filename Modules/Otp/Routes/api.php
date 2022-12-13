<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Modules\Otp\Http\Controllers\V1\Api\ApiAuthenticateController as V1ApiAuthenticateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function (Router $router) {
    $router->post('login', [V1ApiAuthenticateController::class, 'requestOtp'])
        ->name('otp.v1.api-authenticate.request-otp.post.api')
        ->middleware('throttle:5,1');
    $router->post('confirm', [V1ApiAuthenticateController::class, 'confirmOtp'])
        ->name('otp.v1.api-authenticate.confirm-otp.post.api')
        ->middleware('throttle:4,1');
    $router->post('resend', [V1ApiAuthenticateController::class, 'resendOtp'])
        ->name('otp.v1.api-authenticate.resend-otp.post.api')
        ->middleware('throttle:2,2');
});
