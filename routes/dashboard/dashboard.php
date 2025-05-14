<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\ProductController;
use App\Http\Controllers\dashboard\CategoryController;
use App\Http\Controllers\dashboard\AdminAuthController;
use App\Http\Controllers\dashboard\MenuController;
use App\Http\Controllers\dashboard\BrandController;
use App\Http\Controllers\dashboard\PackageController;
use App\Http\Controllers\dashboard\MachineController;
use App\Http\Controllers\dashboard\StationsController;
use App\Http\Controllers\dashboard\Settings\App\SliderController;
use App\Http\Controllers\dashboard\Settings\App\OnboardingController;


Route::prefix('admin')->group(function () {


    Route::controller(AdminAuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::middleware('auth:admin-api')->group(function () {
            Route::post('/logout', 'logout');
            Route::get('/profile', 'profile');
        });
    });

    // Menu Routes
    Route::controller(MenuController::class)->group(function () {
        Route::get('/crmMenu', 'crmMenu')->name('menu.crm');
    });

    Route::apiResource('products', ProductController::class)->except(['update']);
    Route::prefix('products')->controller(ProductController::class)->group(function () {
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/images/{id}', 'deleteImage');
    });


// Category Routes

    Route::apiResource('categories', CategoryController::class)->except(['update']);
    Route::prefix('categories')->controller(CategoryController::class)->group(function () {
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/images/{id}', 'deleteImage');

    });

// Brand Routes

    Route::apiResource('brands', BrandController::class)->except(['update']);
    Route::prefix('brands')->controller(BrandController::class)->group(function () {
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/images/{id}', 'deleteImage');

    });


    Route::apiResource('packages', PackageController::class)->except(['update']);
    Route::prefix('packages')->controller(PackageController::class)->group(function () {
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/images/{id}', 'deleteImage');
        Route::post('/customize/{id}', 'customize');
    });


    Route::apiResource('machines', MachineController::class)->except(['update']);
    Route::prefix('machines')->controller(MachineController::class)->group(function () {
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/images/{id}', 'deleteImage');
    });

    Route::apiResource('stations', StationsController::class)->except(['update']);
    Route::prefix('stations')->controller(StationsController::class)->group(function () {
        Route::post('/{id}', 'update')->name('update');
        Route::delete('/images/{id}', 'deleteImage');
        Route::post('/set/{id}', 'set');
    });




    Route::apiResource('sliders', SliderController::class)->except(['update']);
    Route::prefix('sliders')->controller(SliderController::class)->group(function () {
        Route::post('/{id}', 'update')->name('update');

    });


    Route::apiResource('onboardings', OnboardingController::class)->except(['update']);
    Route::prefix('onboardings')->controller(OnboardingController::class)->group(function () {
        Route::post('/{id}', 'update')->name('update');
    });


});





