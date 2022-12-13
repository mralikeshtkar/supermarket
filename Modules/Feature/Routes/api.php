<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Feature\Http\Controllers\V1\Api\ApiFeatureController as V1ApiFeatureController;
use Modules\Feature\Http\Controllers\V1\Api\ApiAttributeController as V1ApiAttributeController;

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

        $router->post('features/{feature}/attributes', [V1ApiAttributeController::class, 'store'])
            ->name('feature.v1.api-attribute.store.post.api');

        $router->group(['prefix' => 'admin'], function (Router $router) {

            $router->post('features', [V1ApiFeatureController::class, 'store']);
            $router->put( 'features/{feature}', [V1ApiFeatureController::class, 'update']);
            $router->get('features/{feature}', [V1ApiFeatureController::class, 'show']);
            $router->delete('features/{feature}', [V1ApiFeatureController::class, 'destroy']);
            $router->get('features/{feature}/options', [V1ApiFeatureController::class, 'options']);
            $router->post('features/{feature}/options', [V1ApiFeatureController::class, 'storeOptions']);
            $router->delete('features/{feature}/options/{option}', [V1ApiFeatureController::class, 'destroyOptions']);

        });

    });
});
