<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//
//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');
//

Route::get('/categories', function () {
    return [
        'status' => true,
        'code' => 200,
        'message' => 'success',
        'data' => categories(),
    ];

});


//Route::get('/menu', function () {
//    return [
//        'data' => \App\Models\AppMenu::where('appGroupId', 12)
//            ->where('appId', 3)
//            ->orderBy('parentId', 'desc')
//            ->orderBy('id', 'asc')
//            ->get(),
//        'status' => true,
//        'code' => 200,
//        'message' => 'success',
//    ];
//
//
//});




require __DIR__ . '/dashboard/dashboard.php';
require __DIR__ . '/mobile/mobile.php';
