<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Modules\Media\Http\Controllers\V1\Api\ApiMediaController as V1ApiMediaController;

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
    $router->middleware('auth:sanctum')->group(function (Router $router){
        $router->delete('media/{media}/{collection}', [V1ApiMediaController::class,'destroy'])->name('media.v1.api-media.destroy.destroy.api');
    });
});
