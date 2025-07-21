<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Token;

class AuthController extends BaseController
{
    use ApiResponse;
    // Register
    public function register(Request $request)
    {
        // ini_set('max_execution_time', 300);

        // DB::table('diseases')->where('name', 'Hand Foot and Mouth Disease')->update([
        //     'name' => 'HFMD (Hand Foot and Mouth Disease)',
        // ]);
        // dd('o');
        // foreach ($myData as $key => $value) {

        //     DB::table('medicines')->where('id', $value['id'])
        //         ->update([
        //             'salt_introduction' => $value['salt_introduction'],
        //         ]);
        // }
        // dd($myData);

        // $data = DB::table('medicines')->select('id','salt', 'salt_introduction')->get();

        // dd($data);

        // $content = Storage::disk('local')->get('disease_medicine_map.json');

        // $data = json_decode($content, true);

        // dd($data['disease_medicine_map']);

        // foreach ($data['disease_medicine_map'] as $key => $value) {
        //     // dd($key);
        //     // dd(implode(',' ,$value));
        //     $salt = [];
        //     foreach ($value as $k => $val) {
        //         $saltD = DB::table('medicines')->select('id', 'salt')->where('name', $val)->first();
        //         if (!isset($saltD->salt)) {
        //             continue;
        //         }
        //         $salt[] = $saltD->salt;
        //     }
        //     // dd($salt);
        //     DB::table('disease_clinical_data')->where('id', $key)
        //         ->update([
        //             'salts' => implode(',', $salt),
        //         ]);
        // DB::table('disease_clinical_data')->where('id', $key)
        //     ->update([
        //         'medicines' => implode(',', $value),
        //     ]);
        // }
        // dd($key);
        // $data = DB::table('disease_clinical_data')->get();
        // $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        // Storage::disk('local')->put('data.json', $jsonData);

        // $content = Storage::disk('local')->get('data.json');

        // $data = json_decode($content, true);


        // dd($data);

        // DB::table('disease_clinical_data')->insert(
        //     $data
        // );

        // DB::table('disease_clinical_data')->truncate();
        // $data = DB::table('diseases')->get();
        // $data = DB::table('disease_clinical_data')->get();
        // dd($data);

        // try {

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
        // } catch (\Throwable $e) {

        //     $this->reportException(__CLASS__ . "/" . __FUNCTION__, $e->getMessage());
        // }
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

    public function authLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        // Generate token
        $user1 = User::find($user->id);
        $tokenResult = $user1->createToken('Personal Access Token');
        $accessToken = $tokenResult->accessToken;
        $refreshToken = $tokenResult->token->id; // not real refresh token, mock

        // Fake roles payload for example
        $roles = [
            [
                'group_id'     => 1,
                'hospital_id'  => 10,
                'branch_id'    => 1,
                'profile_id'   => 1000,
                'role'         => $user->role,
            ],
            // Add more roles if needed
        ];

        return response()->json([
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken, // Normally Passport doesn't use refresh tokens by default
            'token_type'    => 'Bearer',
            'expires_in'    => config('auth.token_lifetime', 3600),
            'email'         => $user->email,
            'user_id'       => $user->id,
            'roles'         => $roles,
            'user'          => $user,
        ]);
    }

    public function refreshToken(Request $request)
    {
        $authorizationHeader = $request->header('Authorization');

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return response()->json(['message' => 'Missing or invalid Authorization header'], 401);
        }

        $refreshTokenId = trim(str_replace('Bearer', '', $authorizationHeader));
        $token = Token::find($refreshTokenId);

        if (!$token || $token->revoked) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 401);
        }

        // Revoke old token
        $token->revoke();

        // Get user and generate new token
        $user = User::find($token->user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $tokenResult = $user->createToken('authToken');
        $newAccessToken = $tokenResult->accessToken;
        $newRefreshToken = $tokenResult->token->id;

        // Roles (example)
        $roles = [
            [
                'group_id'     => 1,
                'hospital_id'  => 10,
                'branch_id'    => 1,
                'profile_id'   => 1000,
                'role'         => 'admin',
            ],
        ];

        return response()->json([
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'Bearer',
            'expires_in' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'roles' => $roles,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function show(int $id)
    {
        // ── Dummy profile records ───────────────────
        $profiles = collect([
            [
                'id'               => 1,
                'name'             => 'Dr. Asha Verma',
                'email'            => 'asha.verma@example.com',
                'employee_type_id' => 101,
                'phone'            => '+91‑98765‑43210',
                'department'       => 'Cardiology',
            ],
            [
                'id'               => 2,
                'name'             => 'Nurse Ravi Singh',
                'email'            => 'ravi.singh@example.com',
                'employee_type_id' => 202,
                'phone'            => '+91‑91234‑56789',
                'department'       => 'Emergency',
            ],
            [
                'id'               => 1000,
                'name'             => 'Admin Maya Kapoor',
                'email'            => 'maya.kapoor@example.com',
                'employee_type_id' => 303,
                'phone'            => '+91‑99887‑77665',
                'department'       => 'Administration',
            ],
        ]);
        // ─────────────────────────────────────────────

        $profile = $profiles->firstWhere('id', $id);

        if (! $profile) {
            return response()->json([
                'message' => "Profile with ID {$id} not found.",
            ], 404);
        }

        return response()->json($profile);   // status 200
    }
}
