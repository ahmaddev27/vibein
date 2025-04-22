<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\mobile\AuthController;
use App\Http\Controllers\mobile\HomeController;

//<!--Route::middleware('auth:admin')->group(function () {-->
//<!--Route::get('dashboard', function () {return view('admin.dashboard');})->name('dashboard');-->
//<!--Route::resource('categories', CategoryController::class)->except('show');-->
//<!--Route::controller(CategoryController::class)->group(function () {-->
//<!--Route::get('categories/details/{id}','details')->name('categories.details');-->
//<!--Route::get('categories/list',  'list')->name('categories.list');-->
//<!--});-->



Route::group(['prefix' => 'mobile'], function () {

    Route::controller(AuthController::class)->group(function () {

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', 'logout');
            Route::get('/profile', 'profile');
        });

        Route::post('/send-otp', 'sendOtp');
        Route::post('/verify-otp', 'verifyOtp');
//        Route::post('/fcm', 'fcm');
    });
    Route::controller(HomeController::class)->group(function () {
        Route::get('/onbording', 'onbording');
        Route::get('/sliders', 'sliders');
    });
});
