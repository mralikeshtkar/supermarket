<?php

use Illuminate\Routing\Router;
use Modules\Storeroom\Http\Controllers\V1\Api\ApiAdminStoreroomController as V1ApiAdminStoreroomController;
use Modules\Storeroom\Http\Controllers\V1\Api\ApiAdminStoreroomEntranceController as V1ApiAdminStoreroomEntranceController;
use Modules\Storeroom\Http\Controllers\V1\Api\ApiStoreroomEntranceController as V1ApiStoreroomEntranceController;
use Modules\Storeroom\Http\Controllers\V1\Api\ApiStoreroomOutController as V1ApiStoreroomOutController;
use Modules\Storeroom\Http\Controllers\V1\Api\ApiStoreroomOutEntranceController as V1ApiStoreroomOutEntranceController;

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
        $router->group([], function (Router $router) {
            $router->match(['put', 'patch'], 'storerooms/{storeroom_entrance}/entrance', [V1ApiStoreroomEntranceController::class, 'update'])
                ->name('storeroom.v1.api-storeroom-entrance.update.patch.api');
        });
        $router->group([], function (Router $router) {
            $router->post('storerooms/out', [V1ApiStoreroomOutController::class, 'store'])
                ->name('storeroom.v1.api-storeroom-out.store.post.api');
            $router->match(['put', 'patch'], 'storerooms/out-entrance/{storeroom_out_entrance}/products', [V1ApiStoreroomOutEntranceController::class, 'update'])
                ->name('storeroom.v1.api-storeroom-out-entrance.store.patch.api');
        });

        $router->group(['prefix' => 'admin'], function (Router $router) {
            $router->group([], function (Router $router) {
                $router->get('storerooms', [V1ApiAdminStoreroomController::class, 'index'])
                    ->name('storeroom.v1.api-admin-storeroom.index.get.api');
                $router->post('storerooms', [V1ApiAdminStoreroomController::class, 'store'])
                    ->name('storeroom.v1.api-admin-storeroom.store.post.api');
                $router->match(['put', 'patch'], 'storerooms/{storeroom}', [V1ApiAdminStoreroomController::class, 'update'])
                    ->name('storeroom.v1.api-admin-storeroom.update.patch.api');
                $router->delete('storerooms/{storeroom}', [V1ApiAdminStoreroomController::class, 'destroy'])
                    ->name('storeroom.v1.api-admin-storeroom.destroy.delete.api');
                $router->get('storerooms/{storeroom}', [V1ApiAdminStoreroomController::class, 'show'])
                    ->name('storeroom.v1.api-admin-storeroom.show.get.api');
                $router->get('storerooms/{storeroom}/products', [V1ApiAdminStoreroomController::class, 'products'])
                    ->name('storeroom.v1.api-admin-storeroom.products.get.api');
            });

            $router->group([], function (Router $router) {
                $router->get('storerooms/{storeroom}/entrance', [V1ApiAdminStoreroomEntranceController::class, 'index'])
                    ->name('storeroom.v1.api-admin-storeroom-entrance.index.get.api');
                $router->post('storerooms/{storeroom}/entrance', [V1ApiAdminStoreroomEntranceController::class, 'store'])
                    ->name('storeroom.v1.api-storeroom-entrance.store.post.api');
                $router->get('entrances/{entrance}/products', [V1ApiAdminStoreroomEntranceController::class, 'products'])
                    ->name('storeroom.v1.api-storeroom-entrance.products.get.api');
                $router->match(['put', 'patch'], 'entrances/{entrance}/products', [V1ApiAdminStoreroomEntranceController::class, 'updateProduct'])
                    ->name('storeroom.v1.api-storeroom-entrance.update-product.patch.api');
                $router->delete('entrances/{entrance}/products', [V1ApiAdminStoreroomEntranceController::class, 'destroyProduct'])
                    ->name('storeroom.v1.api-storeroom-entrance.update-products.delete.api');
            });

            $router->group([], function (Router $router) {

            });
        });
    });
});
