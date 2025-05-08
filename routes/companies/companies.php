<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\company\CompanyAuthController;
use App\Http\Controllers\dashboard\MenuController;


Route::prefix('company')->group(function () {


    Route::controller(CompanyAuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::middleware('auth:company-api')->group(function () {
            Route::post('/logout', 'logout');
            Route::get('/profile', 'profile');
            Route::post('/profile', 'updateProfile');
        });
    });

    // Menu Routes
    Route::controller(MenuController::class)->group(function () {
        Route::get('/crmMenu', 'crmMenu')->name('menu.crm');
    });
});






