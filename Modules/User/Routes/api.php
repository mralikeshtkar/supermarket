<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\User\Http\Controllers\V1\Api\ApiAdminUserController as V1ApiAdminUserController;
use Modules\User\Http\Controllers\V1\Api\ApiCartController as V1ApiCartController;
use Modules\User\Http\Controllers\V1\Api\ApiUserController as V1ApiUserController;

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
        $router->get('user', [V1ApiUserController::class, 'currentUser']);
        $router->put('users', [V1ApiUserController::class, 'update']);
        $router->post('users/like', [V1ApiUserController::class, 'like']);
        $router->post('users/dislike', [V1ApiUserController::class, 'dislike']);
        $router->get('users/favourites', [V1ApiUserController::class, 'favourites']);
        $router->get('users/{user}/favourites', [V1ApiUserController::class, 'userFavourites']);
    });
    $router->middleware('auth:sanctum')->group(function (Router $router) {
        $router->get('user/orders', [V1ApiUserController::class, 'orders']);
        $router->get('user/orders/{order}', [V1ApiUserController::class, 'showOrder']);

        $router->get('users/cart', [V1ApiCartController::class, 'index'])
            ->name('user.v1.api-cart.index.get.api');
        $router->post('users/cart', [V1ApiCartController::class, 'store'])
            ->name('user.v1.api-cart.store.post.api');
        $router->middleware('cart')->group(function (Router $router) {

            $router->post('users/cart/reduce-quantity', [V1ApiCartController::class, 'reduceQuantity']);

        });
        $router->group(['prefix' => 'admin'], function (Router $router) {

            $router->get('users', [V1ApiAdminUserController::class, 'index']);
            $router->post('users', [V1ApiAdminUserController::class, 'store']);
            $router->get('users/{user}', [V1ApiAdminUserController::class, 'show']);
            $router->match(['put', 'patch'], 'users/{user}', [V1ApiAdminUserController::class, 'update']);
            $router->delete('users/{user}', [V1ApiAdminUserController::class, 'destroy']);
            $router->get('user', [V1ApiAdminUserController::class, 'user']);

            /* Online */
            $router->get('users/online', [V1ApiAdminUserController::class, 'online']);

            /* Cart */
            $router->get('users/{user}/cart', [V1ApiAdminUserController::class, 'cart']);

            /* Orders */
            $router->get('users/{user}/orders', [V1ApiAdminUserController::class, 'orders']);
        });
    });
});
