<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

trait ApiResponse
{
    protected function success($data = [], $message = 'Success', $status = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function error($message = 'Something went wrong', $status = 500, $data = []): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function encryptId($id)
    {
        try {
            return Crypt::encryptString($id);
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function decryptId($encryptedId)
    {
        try {
            return Crypt::decryptString($encryptedId);
        } catch (DecryptException $e) {
            return null; // or handle error
        }
    }

    protected function shortEncrypt($string, $key = 'my-treint-secret')
    {
        $combined = $string . '::' . $key;
        return rtrim(strtr(base64_encode($combined), '+/', '-_'), '=');
    }

    // Decode (not really secure, just demonstration)
    protected function shortDecrypt($encoded, $key = 'my-treint-secret')
    {
        $decoded = base64_decode(strtr($encoded, '-_', '+/') . str_repeat('=', 3 - (strlen($encoded) % 4)));
        return str_replace('::' . $key, '', $decoded);
    }
}
