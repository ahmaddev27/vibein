<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\AppUser;
use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResources;

class AuthController extends Controller
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

    public function sendOtp(Request $request)
    {
        $rules = [
            'phone' => ['required', 'regex:/^[5][0-9]{8}$/'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorMessage = implode(" ", array_map(fn($field) => $errors[$field][0], array_keys($errors)));
            return $this->apiRespose($errors, $errorMessage, false, 422);
        }

        $phone = $request->phone;

        try {
            // Generate OTP
            $otp = '1234'; // For testing, use rand(100000, 999999) for production

            Cache::put("otp_{$phone}", $otp, now()->addMinutes(5));

            $user = AppUser::where('phone', $phone)->first();

            $message = $user ? 'otp send to login' : 'otp send to register';
            return $this->apiRespose(['otp' => $otp], $message, true, 200);
        } catch (\Exception $e) {
            Log::error('Error in sendOtp: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);
            return $this->apiRespose([], 'Something went wrong', false, 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $rules = [
            'phone' => ['required', 'regex:/^[5][0-9]{8}$/'],
            'otp' => 'required',
            'fcm_token' => 'required', // Ensure fcm_token is part of the request
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorMessage = implode(" ", array_map(fn($field) => $errors[$field][0], array_keys($errors)));
            return $this->apiRespose($errors, $errorMessage, false, 400);
        }

        $phone = $request->phone;
        $otp = $request->otp;
        $fcmToken = $request->fcm_token;

        try {
            // Check OTP validity
            $cachedOtp = Cache::get("otp_{$phone}");

            if (!$cachedOtp || $cachedOtp != $otp) {
                return $this->apiRespose(['errors' => ['incorrect-otp']], 'incorrect-otp', false, 400);
            }

            $user = AppUser::where('phone', $phone)->first();

            if ($user) {
                // Check if token exists without a user_id (orphaned token)
                $existingOrphanToken = FcmToken::where('token', $fcmToken)
                    ->whereNull('user_id')
                    ->first();

                if ($existingOrphanToken) {
                    // Claim the orphaned token for this user
                    $existingOrphanToken->update(['user_id' => $user->id]);
                } else {
                    // Check if token already exists for this user
                    $user->fcm_tokens()->firstOrCreate(
                        ['token' => $fcmToken],
                        ['token' => $fcmToken]
                    );
                }
            } else {
                // Check if token exists without a user_id
                $existingOrphanToken = FcmToken::where('token', $fcmToken)
                    ->whereNull('user_id')
                    ->first();

                if ($existingOrphanToken) {
                    // Create user and associate existing token
                    $user = AppUser::create([
                        'name' => $phone,
                        'phone' => $phone,
                    ]);

                    $existingOrphanToken->update(['user_id' => $user->id]);
                } else {
                    // Create new user and new token
                    $user = AppUser::create([
                        'name' => $phone,
                        'phone' => $phone,
                    ]);

                    $user->fcm_tokens()->create(['token' => $fcmToken]);
                }
            }

            return $this->apiRespose([
                'token' => $user->createToken('mobile-app-token')->plainTextToken,
                'user' => new UserResources($user),
            ], $user->wasRecentlyCreated ? 'successfully register' : 'successfully login', true, 200);

        } catch (\Exception $e) {
            Log::error('Error in verifyOtp: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);
            return $this->apiRespose([], 'Something went wrong', false, 500);
        }
    }



    public function profile()
    {

        return $this->apiRespose(
            new UserResources(Auth::user()), 'successfully', true, 200);

    }


    public function logout()
    {

        Auth::user()->tokens()->delete();

        return $this->apiRespose(
            [], 'successfully logout', true, 200);

    }


}
