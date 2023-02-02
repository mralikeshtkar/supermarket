<?php

use Illuminate\Routing\Router;
use Modules\Vote\Http\Controllers\V1\Api\Admin\ApiAdminVoteController as V1ApiAdminVoteController;
use Modules\Vote\Http\Controllers\V1\Api\Admin\ApiAdminVoteItemController as V1ApiAdminVoteItemController;
use Modules\Vote\Http\Controllers\V1\Api\ApiVoteController as V1ApiVoteController;
use Modules\Vote\Http\Controllers\V1\Api\ApiVoteItemController as V1ApiVoteItemController;

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

            /* Votes */
            $router->get('votes',[V1ApiAdminVoteController::class,'index']);
            $router->get('votes/{vote}',[V1ApiAdminVoteController::class,'show']);
            $router->post('votes',[V1ApiAdminVoteController::class,'store']);
            $router->put('votes/{vote}',[V1ApiAdminVoteController::class,'update']);
            $router->delete('votes/{vote}',[V1ApiAdminVoteController::class,'destroy']);

            /* Vote items */
            $router->post('vote-items',[V1ApiAdminVoteItemController::class,'store']);
            $router->put('vote-items/{voteItem}',[V1ApiAdminVoteItemController::class,'update']);
            $router->delete('vote-items/{voteItem}',[V1ApiAdminVoteItemController::class,'destroy']);

        });

        /* Votes */
        $router->get('votes',[V1ApiVoteController::class,'index']);

        /* Vote items */
        $router->post('vote-items/{voteItem}',[V1ApiVoteItemController::class,'store']);

    });

});
