<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'password' => 'required|string|min:8|confirmed',
            Rules\Password::defaults(),
        ], [
            'name.required' => 'Nameeee is required',
            'email.required' => 'Emailllll is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email already exists',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
        ], [
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
        ]);
        if ($validation->fails()) {
            return ApiResponse::ResponseMessage($validation->messages()->all(), 'Register Validation Error', 422);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $data['token'] = $user->createToken('auth_token')->plainTextToken;
        $data['name'] = $user->name;
        $data['email'] = $user->email;

        return ApiResponse::ResponseMessage($data, 'User Created Successfully', 201);
    }
    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required',
        ], [
            'name.required' => 'Nameeee is required',
            'email.required' => 'Emailllll is required',
            'email.email' => 'Email must be a valid email address',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
        ], [
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
        ]);
        if ($validation->fails()) {
            return ApiResponse::ResponseMessage($validation->errors()->all(), 'Register Validation Error', 422);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $data['token'] = $user->createToken('auth_token')->plainTextToken;
            $data['name'] = $user->name;
            $data['email'] = $user->email;
            return ApiResponse::ResponseMessage($data, 'User Logged in Successfully', 200);
        } else {
            return ApiResponse::ResponseMessage('Invalid Credentials', 'Login Error', 401);
        }
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::ResponseMessage([], 'Logout Success', 200);
    }
}
