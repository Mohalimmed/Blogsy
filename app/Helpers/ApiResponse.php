<?php

namespace App\Helpers;

class ApiResponse
{
    static function ResponseMessage($data = null, $message = null, $code = 200)
    {

        $response = [
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ];
        return response()->json($response, $code);
    }
}
