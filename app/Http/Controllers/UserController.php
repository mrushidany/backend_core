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

    public function login(Request $request)
    {
        //Validation of the incoming post requests from the login form in the Frontend Application
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|min:6',
        ]);

        // Checkin if the validation fails
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->toArray()
            ], 500);
        }

        //Assigning the email and password requests into credentials variable for fetching from users table from the database
        $credentials = $request->only(['email','password']);
        $user = User::where('email', $credentials['email'])->first();

        //Testing the if the user exists
        if($user)
        {
            if(! auth()->attempt($credentials)){
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email or password',
                    'error' => 'Invalid email or password'
                ], 422);
            }
            //Creation of access token
            $access_token = auth()->user()->createToken('authToken')->accessToken;
            $response_message = 'Login Succesfull';
            return $this->respond_with_token($access_token, $response_message, auth()->user());
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Sorry this user does not exist',
                'error' => 'Sorry this user does not exist'
            ], 422);
        }

    }
}
