<?php

use Illuminate\Routing\Router;
use Modules\Rack\Http\Controllers\V1\Api\ApiAdminRackController as ApiApiAdminRackController;
use Modules\Rack\Http\Controllers\V1\Api\ApiAdminRackRowController as V1ApiAdminRackRowController;
use Modules\Rack\Http\Controllers\V1\Api\ApiRackController as V1ApiRackController;
use Modules\Rack\Http\Controllers\V1\Api\ApiRackRowController as V1ApiRackRowController;

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

        /* Rack rows */
        $router->delete('rack-rows/{rack_row}', [V1ApiRackRowController::class, 'destroy'])->name('rack.v1.api-rack-row.destroy.delete.api');

        $router->group(['prefix' => 'admin'], function (Router $router) {

            /* Racks */
            $router->get('racks', [ApiApiAdminRackController::class, 'index'])
                ->name('rack.v1.api-admin-rack.index.get.api');
            $router->post('racks', [ApiApiAdminRackController::class, 'store'])
                ->name('rack.v1.api-admin-rack.store.post.api');
            $router->match(['put', 'patch'], 'racks/change-sort', [ApiApiAdminRackController::class, 'changeSort'])
                ->name('rack.v1.api-admin-rack.change-sort.patch.api');
            $router->match(['put', 'patch'], 'racks/{rack}', [ApiApiAdminRackController::class, 'update'])
                ->name('rack.v1.api-admin-rack.update.patch.api');
            $router->get('racks/{rack}', [ApiApiAdminRackController::class, 'show'])
                ->name('rack.v1.api-admin-rack.show.get.api');
            $router->delete('racks/{rack}', [ApiApiAdminRackController::class, 'destroy'])
                ->name('rack.v1.api-admin-rack.destroy.delete.api');
            $router->match(['put', 'patch'], 'racks/{rack}/accept', [ApiApiAdminRackController::class, 'accept'])
                ->name('rack.v1.api-admin-rack.accept.patch.api');
            $router->match(['put', 'patch'], 'racks/{rack}/reject', [ApiApiAdminRackController::class, 'reject'])
                ->name('rack.v1.api-admin-rack.reject.patch.api');
            $router->get('racks/{rack}', [ApiApiAdminRackController::class, 'show'])
                ->name('rack.v1.api-admin-rack.show.get.api');
            $router->match(['put', 'patch'], 'racks/{rack}/rows/change-sort', [ApiApiAdminRackController::class, 'changeSortRows'])
                ->name('rack.v1.api-admin-rack.change-sort-rows.get.api');

            /* Rows */
            $router->post('racks/{rack}/rows', [V1ApiAdminRackRowController::class, 'store'])
                ->name('rack.v1.api-admin-rack-row.store.post.api');
            $router->get('rows/{rack}/products', [V1ApiAdminRackRowController::class, 'products'])
                ->name('rack.v1.api-admin-rack-row.products.get.api');
            $router->get('rows/{rack_row}', [V1ApiAdminRackRowController::class, 'show'])
                ->name('rack.v1.api-admin-rack-row.show.get.api');
            $router->match(['put', 'patch'], 'rows/{rack_row}', [V1ApiAdminRackRowController::class, 'update'])
                ->name('rack.v1.api-admin-rack-row.update.patch.api');
            $router->delete('rows/{rack_row}', [V1ApiAdminRackRowController::class, 'destroy'])
                ->name('rack.v1.api-admin-rack-row.destroy.delete.api');
            $router->match(['put', 'patch'], 'rows/{rack_row}/active', [V1ApiAdminRackRowController::class, 'active'])
                ->name('rack.v1.api-admin-rack-row.active.patch.api');
            $router->match(['put', 'patch'], 'rows/{rack_row}/inactive', [V1ApiAdminRackRowController::class, 'inactive'])
                ->name('rack.v1.api-admin-rack-row.active.patch.api');
            $router->post('rows/{rack_row}/products/attach', [V1ApiAdminRackRowController::class, 'attach'])
                ->name('rack.v1.api-admin-rack-row.attach.post.api');
            $router->post('rows/{rack_row}/products/detach', [V1ApiAdminRackRowController::class, 'detach'])
                ->name('rack.v1.api-admin-rack-row.detach.post.api');
            $router->match(['put','patch'],'rows/{rack_row}/products/change-sort', [V1ApiAdminRackRowController::class, 'changeSort'])
                ->name('rack.v1.api-admin-rack-row.change-sort.post.api');
        });
    });
    $router->get('racks/rows/products', [V1ApiRackController::class, 'products'])->name('rack.v1.api-rack.products.get.api');
});
