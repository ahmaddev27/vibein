<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Vibein\ProductController;
use App\Http\Controllers\Vibein\PackageController;


Route::prefix('vibein')->group(function () {


    Route::apiResource('products', ProductController::class)->except(['update']);
    Route::prefix('products')->controller(ProductController::class)->group(function () {
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/images/{id}', 'deleteImage');
    });



    Route::apiResource('packages', PackageController::class)->except(['update']);
    Route::prefix('packages')->controller(PackageController::class)->group(function () {
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/images/{id}', 'deleteImage');
        Route::post('/customize/{id}', 'customize');
        Route::get('/get/DeliveriesTime', 'getDeliveriesTime');
    });



});





