<?php

namespace App\GraphQL;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthResolver
{
    // register
    public function register($root, array $arguments)
    {
        try {
            $validator = Validator::make($arguments, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6'
            ]);

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'message' => 'The validation is failed',
                    'errors' => $validator->errors()->all(),
                ];
            }

            $user = User::create([
                'name' => $arguments['name'],
                'email' => $arguments['email'],
                'password' => Hash::make($arguments['password']),
            ]);

            $token = $user->createToken($user->email)->plainTextToken;

            return [
                'success' => true,
                'message' => 'Registration successful',
                'errors' => [],
                'token' => $token,
                'user' => $user,
            ];
        } catch (\Throwable $th) {
            throw new \Exception('Register failed !, ' . $th->getMessage());
        }
    }

    // login
    public function login($root, array $arguments)
    {
        try {
            $validator = Validator::make($arguments, [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'message' => 'The validation is failed',
                    'errors' => $validator->errors()->all(),
                ];
            }

            if (Auth::guard('web')->attempt(['email' => $arguments['email'], 'password' => $arguments['password']])) {
                $user = Auth::guard('web')->user();
                $token = $user->createToken($user->email)->plainTextToken;
                return [
                    'success' => true,
                    'message' => 'Login successful',
                    'errors' => [],
                    'token' => $token,
                    'user' => $user
                ];
            }

            return [
                'success' => false,
                'message' => 'Your credentials are incorrect',
                'errors' => [],
            ];
        } catch (\Throwable $th) {
            throw new \Exception('Login failed !, ' . $th->getMessage());
        }
    }

    // logout
    public function logout($root, array $arguments, $context)
    {
        $user = $context->user();
        $user->currentAccessToken()->delete();
        return true;
    }
}