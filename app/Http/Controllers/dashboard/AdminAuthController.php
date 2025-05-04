<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResources;
use App\Models\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Validator;


class AdminAuthController extends Controller
{

    use ApiResponseTrait;


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

        $token = $admin->createToken('dashboard-token')->plainTextToken;

        return response()->json([
            'statusCode'=>200,
            'data'=> new AdminResources($admin),
            'message'=>'success',
        ]);



    }


    public function profile()
    {
        return $this->apiRespose(
            new AdminResources(Auth::user()), 'success', true, 200);

    }


    public function logout()
    {
        if (!Auth::check()) {
            return $this->apiRespose([], 'You are not logged in.', false, 400);
        }

        Auth::user()->tokens()->delete();
        return $this->apiRespose([], 'logout success', true, 200);

    }


}
