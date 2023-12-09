<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StreamController;
use Illuminate\Support\Facades\Route;


Route::name('user.')->prefix('/u/{vendor}/')->controller(\App\Http\Controllers\UserVendorController::class)->group(function () {
    Route::get('/products', 'getProductsVendorInterfaceForUser')->name('vendor.interface.products');
    Route::get('/collections', 'getCollectionsVendorInterfaceForUser')->name('vendor.interface.collections');
    Route::get('/{product}/product', 'getProductForUser')->name('vendor.interface.product');
    Route::controller(CartController::class)->middleware('auth:api')->prefix('/cart/{product}')->name('cart.')->group(function () {
        Route::post("/add", 'addProductToTheCart')->name('add');
        Route::post("/update", 'updateProductQuantity')->name('update');
        Route::delete("/remove", 'removeProductfromTheCart')->name('remove');
    });
});

Route::name('user.')->prefix('/u')->group(function () {
    Route::controller(StreamController::class)->group(function () {
        Route::get('vendor/{vendor}/avatar',  'getVendorAvatar')->name('vendor.interface.avatar');
        Route::get('user/{user}/avatar', 'getUserAvatar')->name('user.interface.avatar');
        Route::get('/product/{product}/image', 'getProductImage')->name('interface.product.image');
    });
    
    Route::middleware('auth:api')->post('{vendor}/send-message', [ChatController::class, 'sendVendorAMessage'])->name('send.message');

    Route::apiResource('orders', OrderController::class)->middleware('auth:api')->except(['store','destroy']);
    Route::post('orders/{vendor}/store', [OrderController::class, 'store'])->name('orders.store');
});