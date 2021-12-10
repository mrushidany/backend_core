<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function respond_with_token($token, $response_message, $data)
    {
        return \response()->json([
            'success' => true,
            'message' => $response_message,
            'data' => $data,
            'token' => $token,
            'token_type' => 'bearer',
        ], 200);
    }
}
