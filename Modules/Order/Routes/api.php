<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Order\Http\Controllers\V1\Api\ApiAdminOrderController as V1ApiAdminOrderController;
use Modules\Order\Http\Controllers\V1\Api\ApiOrderController as V1ApiOrderController;

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

        $router->get('user/orders-list',[V1ApiOrderController::class,'index']);
        $router->get('user/orders-list/{order}',[V1ApiOrderController::class,'show']);

        $router->group(['prefix' => 'admin'], function (Router $router) {
            $router->get('orders', [V1ApiAdminOrderController::class, 'index']);
            $router->get('orders/{order}', [V1ApiAdminOrderController::class, 'show']);
            $router->put('orders/{order}/change-status', [V1ApiAdminOrderController::class, 'changeStatus']);
            $router->patch('orders/{order}/delivery-date', [V1ApiAdminOrderController::class, 'deliveryDate']);
            $router->get('orders/{order}/factor', [V1ApiAdminOrderController::class, 'factor']);
        });

        $router->middleware('cart')->group(function (Router $router) {
            $router->post('orders', [V1ApiOrderController::class, 'store']);
        });
    });
});
