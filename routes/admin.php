<?php

use App\Http\Controllers\StreamController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function(){
    Route::get('{admin}/avatar' , [StreamController::class , 'getAdminAvatar'])->name('interface.avatar');
});