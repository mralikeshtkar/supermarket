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
    $router->get('categories', [V1ApiCategoryController::class, 'categories'])
        ->name('category.v1.api-category.categories.get.api');
    $router->get('categories/{category}/products', [V1ApiCategoryController::class, 'products'])
        ->name('category.v1.api-category.products.get.api');
    $router->middleware('auth:sanctum')->group(function (Router $router) {
        $router->match(['put', 'patch'], 'categories/{category}/accept', [V1ApiCategoryController::class, 'accept'])
            ->name('category.v1.api-category.accept.put-patch.api');
        $router->match(['put', 'patch'], 'categories/{category}/reject', [V1ApiCategoryController::class, 'reject'])
            ->name('category.v1.api-category.reject.put-patch.api');
        $router->get('categories/{category}/features', [V1ApiCategoryController::class, 'features'])
            ->name('category.v1.api-category.features.get.api');

        $router->get('categories/{category}/filters',[V1ApiCategoryController::class, 'filters']);

        $router->group(['prefix' => 'admin'], function (Router $router) {

            $router->get('categories/all', [V1ApiAdminCategoryController::class, 'all'])
                ->name('category.v1.api-admin-category.all.get.api');
            $router->get('categories/accepted', [V1ApiAdminCategoryController::class, 'accepted'])
                ->name('category.v1.api-admin-category.accepted.get.api');
            $router->get('categories/{category?}', [V1ApiAdminCategoryController::class, 'index'])
                ->name('category.v1.api-admin-category.index.get.api');
            $router->get('categories/{category}/show', [V1ApiAdminCategoryController::class, 'show'])
                ->name('category.v1.api-admin-category.show.get.api');
            $router->post('categories', [V1ApiAdminCategoryController::class, 'store'])
                ->name('category.v1.api-admin-category.store.post.api');
            $router->match(['put', 'patch'], 'categories/{category}', [V1ApiAdminCategoryController::class, 'update'])
                ->name('category.v1.api-admin-category.update.patch.api');
            $router->delete('categories/{category}', [V1ApiAdminCategoryController::class, 'destroy'])
                ->name('category.v1.api-admin-category.destroy.delete.api');

            $router->get('categories/{category}/features/{feature?}',[V1ApiAdminCategoryController::class, 'features']);

        });
    });
});
