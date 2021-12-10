<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->user = new User;
    }

    public function register(Request $request)
    {
       //Validate first the incoming authentication requests
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Test the validator if it failed
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray(),
             ], 500);
        }

        //Assign the credentials into key value pair for user creation after passing validation
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ];

        //Create new user
        $this->user->create($data);
        //return response to the frontend application
        return response()->json([
            'success' => true,
            'message' => 'Registration Successful',
        ], 200);
    }
}
