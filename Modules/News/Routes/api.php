<?php

use Illuminate\Routing\Router;
use Modules\News\Http\Controllers\V1\Api\Admin\ApiAdminNewsCategoryController as V1ApiAdminNewsCategoryController;
use Modules\News\Http\Controllers\V1\Api\Admin\ApiAdminNewsController as V1ApiAdminNewsController;
use Modules\News\Http\Controllers\V1\Api\Admin\ApiAdminNewsCommentController as V1ApiAdminNewsCommentController;
use Modules\News\Http\Controllers\V1\Api\ApiNewsCategoryController as V1ApiNewsCategoryController;
use Modules\News\Http\Controllers\V1\Api\ApiNewsCommentController as V1ApiNewsCommentController;
use Modules\News\Http\Controllers\V1\Api\ApiNewsController as V1ApiNewsController;

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

            /* News */
            $router->get('news',[V1ApiAdminNewsController::class,'index']);
            $router->post('news',[V1ApiAdminNewsController::class,'store']);
            $router->put('news/{news}',[V1ApiAdminNewsController::class,'update']);
            $router->delete('news/{news}',[V1ApiAdminNewsController::class,'destroy']);

            /* News categories */
            $router->get('news-categories/{newsCategory?}', [V1ApiAdminNewsCategoryController::class, 'index']);
            $router->get('news-categories', [V1ApiAdminNewsCategoryController::class, 'store']);
            $router->put('news-categories/{newsCategory}', [V1ApiAdminNewsCategoryController::class, 'update']);
            $router->delete('news-categories/{newsCategory}', [V1ApiAdminNewsCategoryController::class, 'destroy']);

            /* News comments */
            $router->get('news-comments',[V1ApiAdminNewsCommentController::class,'index']);
            $router->get('news-comments/{newsComment}',[V1ApiAdminNewsCommentController::class,'show']);
            $router->put('news-comments/{newsComment}',[V1ApiAdminNewsCommentController::class,'update']);
            $router->delete('news-comments/{newsComment}',[V1ApiAdminNewsCommentController::class,'destroy']);
        });

        /* News */
        $router->get('news', [V1ApiNewsController::class,'index']);
        $router->get('news/{news}/comments', [V1ApiNewsController::class,'comments']);

        /* News categories */
        $router->get('news-categories/{newsCategory?}', [V1ApiNewsCategoryController::class,'index']);
        $router->get('news-categories/{newsCategory}/news', [V1ApiNewsCategoryController::class, 'news']);

        /* News comments */
        $router->post('news-comments', [V1ApiNewsCommentController::class,'store']);

    });

});
