<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
}
