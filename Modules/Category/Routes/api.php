<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Category\Http\Controllers\V1\Api\ApiAdminCategoryController as V1ApiAdminCategoryController;
use Modules\Category\Http\Controllers\V1\Api\ApiCategoryController as V1ApiCategoryController;

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
    $router->get('categories/{category}/products', [V1ApiCategoryController::class, 'products'])
        ->name('category.v1.api-category.products.get.api');
    $router->middleware('auth:sanctum')->group(function (Router $router) {

        $router->get('categories', [V1ApiCategoryController::class, 'categories']);
        $router->get('categories/{category}/features', [V1ApiCategoryController::class, 'features'])
            ->name('category.v1.api-category.features.get.api');

        $router->get('categories/{category}/filters', [V1ApiCategoryController::class, 'filters']);

        $router->group(['prefix' => 'admin'], function (Router $router) {

            $router->get('categories/all', [V1ApiAdminCategoryController::class, 'all']);
            $router->get('categories/accepted', [V1ApiAdminCategoryController::class, 'accepted']);
            $router->get('categories/{category?}', [V1ApiAdminCategoryController::class, 'index']);
            $router->get('categories/{category}/show', [V1ApiAdminCategoryController::class, 'show']);
            $router->post('categories', [V1ApiAdminCategoryController::class, 'store']);
            $router->match(['put', 'patch'], 'categories/{category}', [V1ApiAdminCategoryController::class, 'update']);
            $router->delete('categories/{category}', [V1ApiAdminCategoryController::class, 'destroy']);
            $router->put('categories/{category}/change-status', [V1ApiAdminCategoryController::class, 'changeStatus']);

            $router->get('categories/{category}/features/{feature?}', [V1ApiAdminCategoryController::class, 'features']);

        });
    });
});
