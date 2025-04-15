<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\ProductController;
use App\Http\Controllers\dashboard\CategoryController;

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


    Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'index');
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories', 'index');
    });



});





