<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\StreamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::name('user.')->prefix('/u/{vendor}/')->controller(\App\Http\Controllers\UserVendorController::class)->group(function () {
    Route::get('/products', 'getProductsVendorInterfaceForUser')->name('vendor.interface.products');
    Route::get('/collections', 'getCollectionsVendorInterfaceForUser')->name('vendor.interface.collections');
    Route::get('/{product}/product', 'getProductForUser')->name('vendor.interface.product');
});

Route::prefix('/u')->group(function () {
    Route::name('user.')->controller(StreamController::class)->group(function () {
        Route::get('vendor/{vendor}/avatar',  'getVendorAvatar')->name('vendor.interface.avatar');
        Route::get('user/{user}/avatar', 'getUserAvatar')->name('user.interface.avatar');
        Route::get('/product/{product}/image' , 'getProductImage')->name('interface.product.image');
    });
    Route::middleware('auth:api')->post('{vendor}/send-message', [ChatController::class, 'sendVendorAMessage'])->name('user.send.message');
});