<?php

use Illuminate\Routing\Router;
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
        $router->post('discounts', [V1ApiDiscountController::class, 'store']);
        $router->match(['put','patch'],'discounts/{discount}', [V1ApiDiscountController::class, 'update']);
        $router->delete('discounts/{discount}', [V1ApiDiscountController::class, 'destroy']);
    });
});
