<?php

use Illuminate\Routing\Router;
use Modules\Address\Http\Controllers\V1\Api\ApiAdminProvinceController as V1ApiAdminProvinceController;
use Modules\Address\Http\Controllers\V1\Api\ApiCityController as V1ApiCityController;
use Modules\Address\Http\Controllers\V1\Api\ApiProvinceController as V1ApiProvinceController;
use Modules\Address\Http\Controllers\V1\Api\ApiAddressController as V1ApiAddressController;

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

/**
 * Address routes.
 */
Route::prefix('v1')->group(function (Router $router) {
    $router->middleware('auth:sanctum')->group(function (Router $router) {
        $router->get('addresses', [V1ApiAddressController::class, 'addresses'])
            ->name('address.v1.api-address.addresses.get.api');
        $router->get('addresses/{address}', [V1ApiAddressController::class, 'show'])
            ->name('address.v1.api-address.show.get.api');
        $router->post('addresses', [V1ApiAddressController::class, 'store'])
            ->name('address.v1.api-address.store.post.api');
        $router->match(['put', 'patch'], 'addresses/{address}', [V1ApiAddressController::class, 'update'])
            ->name('address.v1.api-address.update.put-patch.api');
        $router->delete('addresses/{address}', [V1ApiAddressController::class, 'destroy'])
            ->name('address.v1.api-address.destroy.delete.api');
    });
});

/**
 * Province routes.
 */
Route::prefix('v1')->group(function (Router $router) {
    $router->get('provinces/{province}', [V1ApiProvinceController::class, 'show'])
        ->name('address.v1.api-province.show.get.api');
    $router->get('provinces', [V1ApiProvinceController::class, 'provinces'])
        ->name('address.v1.api-province.provinces.get.api');
    $router->middleware('auth:sanctum')->group(function (Router $router) {
        $router->post('provinces', [V1ApiProvinceController::class, 'store'])
            ->name('address.v1.api-province.store.post.api');
        $router->match(['put', 'patch'], 'provinces/{province}', [V1ApiProvinceController::class, 'update'])
            ->name('address.v1.api-province.update.put-patch.api');
        $router->delete('provinces/{province}', [V1ApiProvinceController::class, 'destroy'])
            ->name('address.v1.api-province.destroy.delete.api');
        /*$router->group(['prefix' => 'admin'], function (Router $router) {
            $router->get('provinces/all', [V1ApiAdminProvinceController::class, 'all'])
                ->name('address.v1.api-admin-province.all.get.api');
            $router->get('provinces/{province}', [V1ApiAdminProvinceController::class, 'show'])
                ->name('address.v1.api-admin-province.show.get.api');
        });*/
    });
});

/**
 * City routes.
 */
Route::prefix('v1')->group(function (Router $router) {
    $router->get('cities/{province}', [V1ApiCityController::class, 'show'])
        ->name('address.v1.api-city.show.get.api');
    $router->middleware('auth:sanctum')->group(function (Router $router) {
        $router->post('cities', [V1ApiCityController::class, 'store'])
            ->name('address.v1.api-city.store.post.api');
        $router->match(['put', 'patch'], 'cities/{province}', [V1ApiCityController::class, 'update'])
            ->name('address.v1.api-city.update.put-patch.api');
        $router->delete('cities/{province}', [V1ApiCityController::class, 'destroy'])
            ->name('address.v1.api-city.destroy.delete.api');
    });
});

