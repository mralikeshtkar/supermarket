<?php

use Illuminate\Routing\Router;
use Modules\Comment\Http\Controllers\V1\Api\Admin\ApiAdminCommentController as V1ApiAdminCommentController;
use Modules\Comment\Http\Controllers\V1\Api\ApiCommentController as V1ApiCommentController;

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
        $router->get('comments', [V1ApiCommentController::class, 'index'])
            ->name('comment.v1.api-comment.index.get.api');
        $router->post('comments', [V1ApiCommentController::class, 'store'])
            ->name('comment.v1.api-comment.store.post.api');

        $router->group(['prefix' => 'admin'], function (Router $router) {

            $router->get('comments', [V1ApiAdminCommentController::class, 'index']);
            $router->get('comments/{comment}', [V1ApiAdminCommentController::class, 'show']);
            $router->put('comments/{comment}', [V1ApiAdminCommentController::class, 'update']);
            $router->put('comments/{comment}/change-status', [V1ApiAdminCommentController::class, 'changeStatus']);
            $router->delete('comments/{comment}', [V1ApiAdminCommentController::class, 'destroy']);

        });
    });
});
