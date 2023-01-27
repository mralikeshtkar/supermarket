<?php

use Illuminate\Routing\Router;
use Modules\Poster\Http\Controllers\Api\Admin\ApiAdminPosterController as V1ApiAdminPosterController;
use Modules\Poster\Http\Controllers\Api\ApiPosterController as V1ApiPosterController;

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

            /* Advertisements */
            $router->get('posters',[V1ApiAdminPosterController::class,'index']);
            $router->post('posters',[V1ApiAdminPosterController::class,'store']);
            $router->get('posters/{poster}',[V1ApiAdminPosterController::class,'show']);
            $router->put('posters/{poster}',[V1ApiAdminPosterController::class,'update']);
            $router->delete('posters/{poster}',[V1ApiAdminPosterController::class,'destroy']);

        });

        /* Advertisements */
        $router->get('posters',[V1ApiPosterController::class,'index']);

    });

});
