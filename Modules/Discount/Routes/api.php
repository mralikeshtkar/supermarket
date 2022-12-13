<?php

use Illuminate\Routing\Router;
use Modules\Discount\Http\Controllers\V1\Api\Admin\ApiAdminDiscountController as V1ApiAdminDiscountController;
use Modules\Discount\Http\Controllers\V1\Api\ApiDiscountController as V1ApiDiscountController;

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

            $router->get('discounts', [V1ApiAdminDiscountController::class, 'index']);
            $router->post('discounts', [V1ApiAdminDiscountController::class, 'store']);
            $router->delete('discounts/{discount}', [V1ApiAdminDiscountController::class, 'destroy']);
            $router->put('discounts/{discount}', [V1ApiAdminDiscountController::class, 'update']);
            $router->put('discounts/{discount}/change-status', [V1ApiAdminDiscountController::class, 'changeStatus']);

        });

    });
});
