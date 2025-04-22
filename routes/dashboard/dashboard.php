<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\ProductController;
use App\Http\Controllers\dashboard\CategoryController;
use App\Http\Controllers\dashboard\MenuController;
use App\Http\Controllers\dashboard\BrandController;
use App\Http\Controllers\dashboard\Settings\App\SliderController;
use App\Http\Controllers\dashboard\Settings\App\OnboardingController;

//<!--Route::middleware('auth:admin')->group(function () {-->
//<!--Route::get('dashboard', function () {return view('admin.dashboard');})->name('dashboard');-->
//<!--Route::resource('categories', CategoryController::class)->except('show');-->
//<!--Route::controller(CategoryController::class)->group(function () {-->
//<!--Route::get('categories/details/{id}','details')->name('categories.details');-->
//<!--Route::get('categories/list',  'list')->name('categories.list');-->
//<!--});-->

Route::prefix('admin')->group(function () {


//    Route::controller(AdminAuthController::class)->group(function () {
//        Route::post('login', 'login');
//        Route::middleware('auth:admin-api')->group(function () {
//            Route::post('/logout', 'logout');
//            Route::get('/profile', 'profile');
//        });
//    });

    // Menu Routes
    Route::controller(MenuController::class)->group(function () {
        Route::get('/crmMenu', 'crmMenu')->name('menu.crm');
    });

// Product Routes
    Route::prefix('products')->controller(ProductController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
    });

// Category Routes
    Route::prefix('categories')->controller(CategoryController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/sub', 'SubCategoriesIndex')->name('sub');
        Route::post('/', 'store')->name('store');
    });

// Brand Routes
    Route::prefix('brands')->controller(BrandController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
    });

    Route::apiResource('sliders', SliderController::class)->except(['show', 'update']);
    Route::prefix('sliders')->controller(SliderController::class)->group(function () {
        Route::post('/{id}', 'update')->name('update');

    });


    Route::apiResource('onboardings', OnboardingController::class)->except(['show', 'update']);
    Route::prefix('onboardings')->controller(OnboardingController::class)->group(function () {
        Route::post('/{id}', 'update')->name('update');

    });
});





