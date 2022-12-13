<?php

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

use Illuminate\Routing\Router;
use Modules\LogActivity\Http\Controllers\V1\Api\Admin\ApiAdminLogActivityController as V1ApiAdminLogActivityController;

Route::prefix('v1')->group(function (Router $router) {

    $router->middleware('auth:sanctum')->group(function (Router $router) {

        $router->group(['prefix' => 'admin'], function (Router $router) {
            $router->get('log-activities', [V1ApiAdminLogActivityController::class, 'index']);
        });

    });

});
