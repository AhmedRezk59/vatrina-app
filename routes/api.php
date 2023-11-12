<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::name('user.')->prefix('/u')->controller(\App\Http\Controllers\UserVendorController::class)->group(function (){
    Route::get('/{vendor}/products','getProductsVendorInterfaceForUser')->name('vendor.interface.products');
    Route::get('/{vendor}/collections','getCollectionsVendorInterfaceForUser')->name('vendor.interface.collections');
    Route::get('/{vendor}/{product}/product','getProductForUser')->name('vendor.interface.product');
});
