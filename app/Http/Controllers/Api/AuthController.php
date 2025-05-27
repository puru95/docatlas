<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    use ApiResponse;
    // Register
    public function register(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users',
                'password' => 'required|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => bcrypt($request->password)
            ]);

            $token = $user->createToken('appToken')->accessToken;

            return $this->success($token, 'User registered successfully');
        } catch (\Throwable $e) {

            $this->reportException(__CLASS__ . "/" . __FUNCTION__, $e->getMessage());
        }
    }

    // Login
    public function login(Request $request)
    {

        try {

            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                /** @var \App\Models\MyUserModel $user **/
                $user =  Auth::user();

                if ($user) {
                    $token = $user->createToken('appToken')->accessToken;

                    $data = [
                        'access_token' => $token,
                        'user_id' => $user->id
                    ];
                } else {
                    return $this->error('Unauthorized', 400);
                }

                return $this->success($data, 'Login successful');
            } else {
                return $this->error('Wrong Credentials', 400);
            }
        } catch (\Throwable $e) {

            $this->reportException(__CLASS__ . "/" . __FUNCTION__, $e->getMessage());
        }
    }

    // Logout
    public function logout(Request $request)
    {

        try {
            $request->user()->token()->revoke();

            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out'
            ]);
        } catch (\Throwable $e) {

            $this->reportException(__CLASS__ . "/" . __FUNCTION__, $e->getMessage());
        }
    }

    // Authenticated User
    public function user(Request $request)
    {

        try {

            return response()->json($request->user());
        } catch (\Throwable $e) {

            $this->reportException(__CLASS__ . "/" . __FUNCTION__, $e->getMessage());
        }
    }
}
