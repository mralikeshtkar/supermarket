<?php

use Illuminate\Routing\Router;
use Modules\Brand\Http\Controllers\V1\Api\Admin\ApiAdminBrandController as V1ApiAdminBrandController;
use Modules\Brand\Http\Controllers\V1\Api\ApiBrandController as V1ApiBrandController;

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
        $router->get('brands', [V1ApiBrandController::class, 'index']);
        $router->get('brands/{brand}', [V1ApiBrandController::class, 'show']);

        $router->group(['prefix' => 'admin'], function (Router $router) {
            $router->get('all-brands', [V1ApiAdminBrandController::class, 'allBrands'])
                ->name('brand.v1.api-admin-brand.all-brands.get.api');
            $router->get('brands', [V1ApiAdminBrandController::class, 'index'])
                ->name('brand.v1.api-admin-brand.index.get.api');
            $router->get('brands/{brand}', [V1ApiAdminBrandController::class, 'show'])
                ->name('brand.v1.api-admin-brand.show.get.api');
            $router->match(['put', 'patch'], 'brands/{brand}', [V1ApiAdminBrandController::class, 'update'])
                ->name('brand.v1.api-admin-brand.show.patch.api');
            $router->match(['put', 'patch'], 'brands/{brand}/accept', [V1ApiAdminBrandController::class, 'accept'])
                ->name('brand.v1.api-admin-brand.accept.patch.api');
            $router->match(['put', 'patch'], 'brands/{brand}/reject', [V1ApiAdminBrandController::class, 'reject'])
                ->name('brand.v1.api-admin-brand.reject.patch.api');
            $router->post('brands', [V1ApiAdminBrandController::class, 'store'])
                ->name('brand.v1.api-admin-brand.store.post.api');
            $router->delete('brands/{brand}', [V1ApiAdminBrandController::class, 'destroy'])
                ->name('brand.v1.api-brand.destroy.delete.api');
        });
    });
});
