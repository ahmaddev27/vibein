<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\mobile\AuthController;
use App\Http\Controllers\mobile\HomeController;
use App\Http\Controllers\mobile\ProductController;
use App\Http\Controllers\mobile\PackageController;
use App\Http\Controllers\mobile\AddressController;


Route::group(['prefix' => 'mobile'], function () {

    Route::controller(AuthController::class)->group(function () {

        Route::post('/send-otp', 'sendOtp');
        Route::post('/verify-otp', 'verifyOtp');
    });

    Route::controller(HomeController::class)->group(function () {
        Route::get('/onbording', 'onbording');

    });


    Route::middleware('auth:sanctum')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('/logout', 'logout');
            Route::get('/profile', 'profile');
        });


        Route::controller(HomeController::class)->group(function () {
            Route::get('/sliders', 'sliders');
            Route::get('/categories', 'categories');
        });


        Route::controller(ProductController::class)->prefix('products')->group(function () {
            Route::get('/', 'index');
            Route::get('details/{id}', 'show');
            Route::get('/best', 'best');

        });

        Route::controller(PackageController::class)->prefix('packages')->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
        });

        Route::apiResource('address', AddressController ::class)->except('update');
        Route::prefix('address')->controller(AddressController::class)->group(function () {
            Route::post('/{id}', 'update');
            Route::post('/setDefault/{id}', 'setDefault');
        });
    });


});
