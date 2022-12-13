<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Tag\Http\Controllers\V1\Api\ApiAdminTagController as V1ApiAdminTagController;
use Modules\Tag\Http\Controllers\V1\Api\ApiTagController as V1ApiTagController;

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
            $router->get('tags/all', [V1ApiAdminTagController::class, 'all'])
                ->name('tag.v1.api-admin-tag.all.get.api');
            $router->get('tags', [V1ApiAdminTagController::class, 'index'])
                ->name('tag.v1.api-admin-tag.index.get.api');
            $router->post('tags', [V1ApiAdminTagController::class, 'store'])
                ->name('tag.v1.api-admin-tag.index.store.api');
            $router->get('tags/{tag}', [V1ApiAdminTagController::class, 'show'])
                ->name('tag.v1.api-admin-tag.show.get.api');
            $router->delete('tags/{tag}', [V1ApiAdminTagController::class, 'destroy'])
                ->name('tag.v1.api-admin-tag.destroy.delete.api');
            $router->match(['put', 'patch'], 'tags/{tag}', [V1ApiAdminTagController::class, 'update'])
                ->name('tag.v1.api-tag.update.put-patch.api');
        });
    });
    $router->get('tags/{tag}', [V1ApiTagController::class, 'show'])
        ->name('tag.v1.api-tag.show.get.api');
});
