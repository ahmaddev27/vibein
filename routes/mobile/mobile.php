<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\mobile\AuthController;
use App\Http\Controllers\mobile\HomeController;
use App\Http\Controllers\mobile\ProductController;
use App\Http\Controllers\mobile\PackageController;

//<!--Route::middleware('auth:admin')->group(function () {-->
//<!--Route::get('dashboard', function () {return view('admin.dashboard');})->name('dashboard');-->
//<!--Route::resource('categories', CategoryController::class)->except('show');-->
//<!--Route::controller(CategoryController::class)->group(function () {-->
//<!--Route::get('categories/details/{id}','details')->name('categories.details');-->
//<!--Route::get('categories/list',  'list')->name('categories.list');-->
//<!--});-->


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
    });


});
