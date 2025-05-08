<?php

namespace App\Http\Controllers\company;

use App\Http\Controllers\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResources;
use App\Http\Resources\dashboard\UserDashboardResource;
use App\Models\DashboardUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class CompanyAuthController extends Controller
{

    use ApiResponseTrait;


    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:dashboard_user,email',
            'password' => 'required|string|min:6',
            'mobile' => 'nullable|string|max:20',
            'firebaseToken' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorMessage = implode(" ", array_map(fn($field) => $errors[$field][0], array_keys($errors)));
            return $this->apiRespose($errors, $errorMessage, false, 400);
        }


        $user = DashboardUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile' => $request->mobile,
            'firebaseToken' => $request->firebaseToken,
        ]);


        $token = $user->createToken('dashboard-token')->plainTextToken;

        return response()->json([
            'statusCode' => 200,
            'data' => new UserDashboardResource($user),
            'token' => $token,
            'message' => 'Registration successful',
        ]);

    }

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


        $admin = DashboardUser::where('email', $request->email)->first();


        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['error' => ['Invalid credentials']], 401);
        }

        $token = $admin->createToken('dashboard-token')->plainTextToken;

        return response()->json([
            'statusCode' => 200,
            'data' => new UserDashboardResource($admin),
            'token' => $token,
            'message' => 'success',
        ]);


    }


    public function profile()
    {
        return $this->apiRespose(
            new UserDashboardResource(Auth::user()), 'success', true, 200);

    }


    public function logout()
    {
        if (!Auth::check()) {
            return $this->apiRespose([], 'You are not logged in.', false, 400);
        }

        Auth::user()->tokens()->delete();
        return $this->apiRespose([], 'logout success', true, 200);

    }


    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:dashboard_user,email,' . $user->id,
            'mobile' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorMessage = implode(" ", array_map(fn($field) => $errors[$field][0], array_keys($errors)));
            return $this->apiRespose($errors, $errorMessage, false, 400);
        }

        $data = $request->only(['name', 'email', 'mobile']);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new image
            $imagePath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $imagePath;
        }

        $user->update($data);

        return response()->json([
            'statusCode' => 200,
            'data' => new UserDashboardResource($user),
            'message' => 'Profile updated successfully',
        ]);
    }


}
