<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\CollectionController;
use App\Http\Controllers\Vendor\VendorProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api-vendor')->prefix('vendor')->name('vendor.')->group(function(){
    Route::controller(VendorProfileController::class)->group(function(){
        Route::get('/user' , 'user')->name('user');
        Route::put('/profile/update' , 'updateInfo')->name('updateInfo');
        Route::put('/profile/password/update' , 'updatePassword')->name('updatePassword');
        Route::put('/profile/avatar/update' , 'updateAvatar')->name('updateAvatar');
    });
    
    Route::apiResource('/collections' , CollectionController::class);
    Route::apiResource('/products' , ProductController::class)->except('index');
    Route::get('/{vendor}/products' , [ProductController::class , 'index'])->name('products.index');
    Route::post('{user}/send-message', [ChatController::class, 'sendUserAMessage'])->name('send.message');
});