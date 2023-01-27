<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Advertisement\Http\Controllers\V1\Api\Admin\ApiAdminAdvertisementController as V1ApiAdminAdvertisementController;
use Modules\Advertisement\Http\Controllers\V1\Api\ApiAdvertisementController as V1ApiAdvertisementController;

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

            /* Advertisements */
            $router->get('advertisements',[V1ApiAdminAdvertisementController::class,'index']);
            $router->post('advertisements',[V1ApiAdminAdvertisementController::class,'store']);
            $router->get('advertisements/{advertisement}',[V1ApiAdminAdvertisementController::class,'show']);
            $router->put('advertisements/{advertisement}',[V1ApiAdminAdvertisementController::class,'update']);
            $router->delete('advertisements/{advertisement}',[V1ApiAdminAdvertisementController::class,'destroy']);

        });

        /* Advertisements */
        $router->get('advertisements',[V1ApiAdvertisementController::class,'index']);

    });

});
