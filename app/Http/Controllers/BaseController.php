<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Session;

class BaseController extends Controller
{
    function reportException($path,$msg) {

        Log::info("Exception start");
        Log::error($path);
        Log::error($msg);
        Log::info("Exception Ends");
        // return response()->json(['status'=>false,'msg' => 'Internal Server Error'], 500);

        return response()->json([
            'status' => false,
            'message' => 'Internal Server Error',
            'data' => [],
        ], 500);
    }

    function generateOTP()
    {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    function getEncodedKey()
    {
        $datasession    =   Session();
        $session_token  =   $datasession['_token'];
        $key            =   base64_encode('<key>admin08<key>'.$session_token);
        return $key;
    }
}
