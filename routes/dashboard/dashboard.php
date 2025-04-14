<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\AdminAuthController;

//<!--Route::middleware('auth:admin')->group(function () {-->
//<!--Route::get('dashboard', function () {return view('admin.dashboard');})->name('dashboard');-->
//<!--Route::resource('categories', CategoryController::class)->except('show');-->
//<!--Route::controller(CategoryController::class)->group(function () {-->
//<!--Route::get('categories/details/{id}','details')->name('categories.details');-->
//<!--Route::get('categories/list',  'list')->name('categories.list');-->
//<!--});-->

Route::prefix('admin')->group(function () {

    Route::get('/menu', function () {
        $menuItems = \App\Models\AppMenu::with('children')->where('appGroupId', 12)
            ->where('appId', 3)
            ->whereNull('parentId')
            ->orderBy('order', 'asc')
            ->get();
        return response()->json([
            'data' => $menuItems,
            'status' => true,
            'code' => 200,
            'message' => 'Success',
        ]);
    })->middleware('auth:admin-api');

    Route::controller(AdminAuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::middleware('auth:admin-api')->group(function () {
            Route::post('/logout', 'logout');
            Route::get('/profile', 'profile');
        });
    });



});




