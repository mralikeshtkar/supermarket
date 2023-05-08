<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Dashboard\Http\Controllers\V1\Api\Admin\ApiAdminDashboardController as V1ApiAdminDashboardController;

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
    $router->middleware('auth:sanctum')->group(function (Router $router) {
        $router->group(['prefix' => 'admin'], function (Router $router){
            $router->get('dashboard', [V1ApiAdminDashboardController::class, 'index']);
            $router->group(['excluded_middleware' => 'throttle:api'],function (Router $router){
                $router->get('dashboard/notifications', [V1ApiAdminDashboardController::class, 'notifications']);
            });
        });
    });
});
