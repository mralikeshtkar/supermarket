<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Transportation\Http\Controllers\V1\Api\Admin\ApiAdminTransportationController as V1ApiAdminTransportationController;

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

            /* News */
            $router->get('transportations',[V1ApiAdminTransportationController::class,'index']);
            $router->post('transportations',[V1ApiAdminTransportationController::class,'store']);
            $router->put('transportations/{transportation}',[V1ApiAdminTransportationController::class,'update']);
            $router->delete('transportations/{transportation}',[V1ApiAdminTransportationController::class,'destroy']);

        });

    });

});
