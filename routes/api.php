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



require __DIR__ . '/dashboard/dashboard.php';
require __DIR__ . '/mobile/mobile.php';
