<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\ApiResponsePaginationTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResources;
use App\Models\Admin;
use App\Models\AppUser;
use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResources;
use Illuminate\Validation\Rule;

class AdminAuthController extends Controller
{

    use ApiResponseTrait;

//    public function fcm(Request $request){
//        $rules = [
//            'fcm_token' => 'required', // Ensure fcm_token is part of the request
//        ];
//
//        $validator = Validator::make($request->all(), $rules);
//
//        if ($validator->fails()) {
//            $errors = $validator->errors()->toArray();
//            $errorMessage = implode(" ", array_map(fn($field) => $errors[$field][0], array_keys($errors)));
//            return $this->apiRespose($errors, $errorMessage, false, 400);
//        }
//
//        $existingToken = FcmToken::where('token', $request->fcm_token)->first();
//
//        if (!$existingToken) {
//            FcmToken::create([
//                'token' => $request->fcm_token,
//                ]);
//        }
//
//        return $this->apiRespose([
//            'token' => $request->fcm_token
//        ], 'success', true, 200);
//
//
//    }


    public function login(Request $request)
    {

        $rules = [
            'email' => 'required|email',
            'password' => 'required|string',
         ];

            $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorMessage = implode(" ", array_map(fn($field) => $errors[$field][0], array_keys($errors)));
            return $this->apiRespose($errors, $errorMessage, false, 400);
        }



        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['error' => ['Invalid credentials']], 401);
        }

        $token = $admin->createToken('admin-dashboard-token')->plainTextToken;
        return $this->apiRespose(['token' => $token], 'success', true, 200);


    }




    public function profile()
    {
        return $this->apiRespose(
            new AdminResources(Auth::user()), 'success', true, 200);

    }


    public function logout()
    {
        Auth::user()->tokens()->delete();
        return $this->apiRespose([], 'logout success', true, 200);

    }




}
