<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Setting\Http\Controllers\V1\Api\Admin\ApiAdminSettingController as V1ApiAdminSettingController;
use Modules\Setting\Http\Controllers\V1\Api\ApiSettingController as V1ApiSettingController;

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

        $router->group(['prefix' => 'admin'], function (Router $router) {
            $router->get('settings', [V1ApiAdminSettingController::class, 'index']);
            $router->post('settings', [V1ApiAdminSettingController::class, 'store']);
        });

        $router->get('check-store-is-open', [V1ApiSettingController::class, 'checkStoreIsOpen']);
        $router->get('settings', [V1ApiSettingController::class, 'index']);

    });
});
