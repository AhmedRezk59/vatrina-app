<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminVendorController;
use App\Http\Controllers\StreamController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('auth:api-admin')->group(function(){
    Route::get('{admin}/avatar' , [StreamController::class , 'getAdminAvatar'])->name('interface.avatar');
    Route::apiResource('admins' , AdminController::class)->except('store');
    Route::put('/vendor/{vendor}/ban' , [AdminVendorController::class , 'banVendor'])->name('ban.vendor')->middleware('permission:ban-vendor');
});