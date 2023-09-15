<?php

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
});