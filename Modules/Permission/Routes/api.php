<?php

use Illuminate\Routing\Router;
use Modules\Permission\Http\Controllers\V1\Api\ApiAdminPermissionController as V1ApiAdminPermissionController;
use Modules\Permission\Http\Controllers\V1\Api\ApiAdminRoleController as V1ApiAdminRoleController;
use Modules\Permission\Http\Controllers\V1\Api\ApiPermissionController as V1ApiPermissionController;
use Modules\Permission\Http\Controllers\V1\Api\ApiRoleController as V1ApiRoleController;

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

Route::prefix('v1')->middleware('auth:sanctum')->group(function (Router $router) {
    $router->get('permissions', [V1ApiPermissionController::class, 'permissions'])
        ->name('permission.v1.api-permission.permissions.get.api');
    $router->get('roles', [V1ApiRoleController::class, 'roles'])
        ->name('permission.v1.api-role.permissions.get.api');
    $router->group(['prefix' => 'admin'], function (Router $router) {
        $router->get('all-roles', [V1ApiAdminRoleController::class, 'allRoles'])
            ->name('permission.v1.api-admin-role.allRoles.get.api');
        $router->get('roles', [V1ApiAdminRoleController::class, 'index'])
            ->name('permission.v1.api-admin-role.index.get.api');
        $router->post('roles', [V1ApiAdminRoleController::class, 'store'])
            ->name('permission.v1.api-admin-role.store.post.api');
        $router->get('roles/{role}', [V1ApiAdminRoleController::class, 'show'])
            ->name('permission.v1.api-admin-role.show.get.api');
        $router->match(['put', 'patch'], 'roles/{role}', [V1ApiAdminRoleController::class, 'update'])
            ->name('permission.v1.api-admin-role.update.put-patch.api');
        $router->delete('roles/{role}', [V1ApiAdminRoleController::class, 'destroy'])
            ->name('permission.v1.api-admin-role.update.delete.api');
        $router->get('permissions', [V1ApiAdminPermissionController::class, 'index'])
            ->name('permission.v1.api-admin-permission.index.get.api');
    });
});
