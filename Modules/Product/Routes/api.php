<?php

use Illuminate\Routing\Router;
use Modules\Product\Http\Controllers\V1\Api\Admin\ApiAdminFaqController as V1ApiAdminFaqController;
use Modules\Product\Http\Controllers\V1\Api\Admin\ApiAdminProductAttributeController as V1ApiAdminProductAttributeController;
use Modules\Product\Http\Controllers\V1\Api\Admin\ApiAdminProductCartController as V1ApiAdminProductCartController;
use Modules\Product\Http\Controllers\V1\Api\Admin\ApiAdminProductController as V1ApiAdminProductController;
use Modules\Product\Http\Controllers\V1\Api\Admin\ApiAdminSpecialProductController as V1ApiAdminSpecialProductController;
use Modules\Product\Http\Controllers\V1\Api\ApiAdminProductUnitController as V1ApiAdminProductUnitController;
use Modules\Product\Http\Controllers\V1\Api\ApiFaqController as V1ApiFaqController;
use Modules\Product\Http\Controllers\V1\Api\ApiProductController as V1ApiProductController;
use Modules\Product\Http\Controllers\V1\Api\ApiSpecialProductController as V1ApiSpecialProductController;

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

            /* Product routes  */
            $router->post('products', [V1ApiAdminProductController::class, 'store']);
            $router->get('products', [V1ApiAdminProductController::class, 'index'])
                ->name('product.v1.api-admin-product.index.get.api');
            $router->get('products/search-all', [V1ApiAdminProductController::class, 'searchAll'])
                ->name('product.v1.api-admin-product.search-all.get.api');
            $router->get('products/only-accepted', [V1ApiAdminProductController::class, 'onlyAccepted']);
            $router->get('products/{product}', [V1ApiAdminProductController::class, 'show'])
                ->name('product.v1.api-admin-product.show.get.api');
            $router->delete('products/{product}', [V1ApiAdminProductController::class, 'destroy'])
                ->name('product.v1.api-admin-product.destroy.delete.api');
            $router->get('products/{product}/gallery', [V1ApiAdminProductController::class, 'gallery'])
                ->name('product.v1.api-admin-product.gallery.get.api');
            $router->match(['put', 'patch'], 'products/{product}/change-sort-gallery', [V1ApiAdminProductController::class, 'changeSortGallery'])
                ->name('product.v1.api-admin-product.change-sort-gallery.patch.api');
            $router->post('products/{product}/gallery', [V1ApiAdminProductController::class, 'uploadGallery'])
                ->name('product.v1.api-admin-product.upload-gallery.post.api');
            $router->delete('products/{product}/gallery/{media}', [V1ApiAdminProductController::class, 'destroyGallery'])
                ->name('product.v1.api-admin-product.destroy-gallery.delete.api');
            $router->post('products/{product}/model', [V1ApiAdminProductController::class, 'uploadModel'])
                ->name('product.v1.api-admin-product.upload-model.post.api');
            $router->get('products/{product}/model', [V1ApiAdminProductController::class, 'model'])
                ->name('product.v1.api-admin-product.model.get.api');
            $router->delete('products/{product}/model', [V1ApiAdminProductController::class, 'destroyModel'])
                ->name('product.v1.api-admin-product.destroy-model.delete.api');
            $router->match(['put', 'patch'], 'products/{product}', [V1ApiAdminProductController::class, 'update'])
                ->name('product.v1.api-admin-product.update.patch.api');
            $router->match(['put', 'patch'], 'products/{product}/accept', [V1ApiAdminProductController::class, 'accept'])
                ->name('product.v1.api-admin-product.accept.patch.api');
            $router->match(['put', 'patch'], 'products/{product}/reject', [V1ApiAdminProductController::class, 'reject'])
                ->name('product.v1.api-admin-product.reject.patch.api');

            /* Product unit routes  */
            $router->get('product-units', [V1ApiAdminProductUnitController::class, 'index'])
                ->name('product.v1.api-admin-product-unit.index.get.api');
            $router->get('product-units/accepted', [V1ApiAdminProductUnitController::class, 'productUnitAccepted'])
                ->name('product.v1.api-admin-product-unit.productUnitAccepted.get.api');
            $router->post('product-units', [V1ApiAdminProductUnitController::class, 'store'])
                ->name('product.v1.api-admin-product-unit.store.post.api');
            $router->match(['put', 'patch'], 'product-units/{productUnit}', [V1ApiAdminProductUnitController::class, 'update'])
                ->name('product.v1.api-admin-product-unit.update.patch.api');
            $router->delete('product-units/{productUnit}', [V1ApiAdminProductUnitController::class, 'destroy'])
                ->name('product.v1.api-admin-product-unit.destroy.delete.api');
            $router->match(['put', 'patch'], 'product-units/{productUnit}/accept', [V1ApiAdminProductUnitController::class, 'accept'])
                ->name('product.v1.api-admin-product-unit.accept.patch.api');
            $router->match(['put', 'patch'], 'product-units/{productUnit}/reject', [V1ApiAdminProductUnitController::class, 'reject'])
                ->name('product.v1.api-admin-product-unit.reject.patch.api');
            $router->match(['put', 'patch'], 'product-units/{productUnit}/destroy', [V1ApiAdminProductUnitController::class, 'destroy'])
                ->name('product.v1.api-admin-product-unit.destroy.patch.api');

            /* Special products */
            $router->get('special-products', [V1ApiAdminSpecialProductController::class, 'index']);
            $router->post('special-products/chane-sort', [V1ApiAdminSpecialProductController::class, 'changeSort']);
            $router->post('special-products/{product}', [V1ApiAdminSpecialProductController::class, 'addProduct']);
            $router->delete('special-products/{product}', [V1ApiAdminSpecialProductController::class, 'destroy']);

            /* Product attributes */
            $router->get('products/{product}/attributes', [V1ApiAdminProductAttributeController::class, 'index']);
            $router->post('products/{product}/attributes', [V1ApiAdminProductAttributeController::class, 'store']);

            /* Product cart */
            $router->get('products/cart/{user}', [V1ApiAdminProductCartController::class, 'products']);
            $router->put('products/{product}/cart/{user}', [V1ApiAdminProductCartController::class, 'update']);
            $router->delete('products/{product}/cart/{user}', [V1ApiAdminProductCartController::class, 'destroy']);

            /* Product cart */
            $router->get('products/cart/{user}', [V1ApiAdminProductCartController::class, 'products']);
            $router->put('products/{product}/cart/{user}', [V1ApiAdminProductCartController::class, 'update']);
            $router->delete('products/{product}/cart/{user}', [V1ApiAdminProductCartController::class, 'destroy']);

            /* Product stocks */
            $router->get('storeroom/products/stocks', [V1ApiAdminProductController::class, 'stocks']);
            $router->get('storeroom/products/all-stocks', [V1ApiAdminProductController::class, 'allStocks']);

            /* Faqs */
            $router->get('products/{product}/faqs/{faq?}', [V1ApiAdminFaqController::class, 'index']);
            $router->get('faqs/{faq}', [V1ApiAdminFaqController::class, 'show']);
            $router->put('faqs/{faq}', [V1ApiAdminFaqController::class, 'update']);
            $router->delete('faqs/{faq}', [V1ApiAdminFaqController::class, 'destroy']);
            $router->get('faqs/{faq}/replies', [V1ApiAdminFaqController::class, 'replies']);

        });

        /* Faqs */
        $router->get('products/{product}/faqs', [V1ApiFaqController::class, 'index']);
        $router->get('faqs/{faq}/replies', [V1ApiFaqController::class, 'replies']);
        $router->post('products/{product}/faqs', [V1ApiFaqController::class, 'store']);

        $router->get('products/most-selling-products', [V1ApiProductController::class, 'mostSellingProducts']);
        $router->get('products/latest', [V1ApiProductController::class, 'latest']);
        $router->get('products/latest/seen', [V1ApiProductController::class, 'latestSeen']);
        $router->match(['put', 'patch'], 'products/{product}/change-status', [V1ApiProductController::class, 'changeStatus']);
        $router->get('products/{product}', [V1ApiProductController::class, 'show']);
        $router->get('products/{product}/similar', [V1ApiProductController::class, 'similar']);
        $router->get('products/{category?}', [V1ApiProductController::class, 'index']);
        $router->get('products/{product}/attributes', [V1ApiProductController::class, 'attributes']);
        $router->get('products/compare/{product1}/{product2}', [V1ApiProductController::class, 'compare']);
        $router->get('special-products', [V1ApiSpecialProductController::class, 'index']);
    });
});
