<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse {

        $request->validate([
            'name' =>       ['required', 'string', 'max:255'],
            'email' =>      ['required', 'string', 'max:255'],
            'password' =>   ['required', 'string', 'max:255']
        ]);

        $user_create = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ];

        $user = User::create($user_create);

        if (!$user) {
            return response()->json([
                'message' => 'Ocorreu um erro inesperado'
            ], 500);
        }

        $token = $user->createToken($user->name . ' Auth-Token')->plainTextToken;

        return response()->json([
            'message' => 'User created successfuly',
            'token_type' => 'Bearer',
            'token' => $token
        ], 201);
    }

    public function login(Request $request): JsonResponse {

        $request->validate([
            'email' =>      ['required', 'string', 'max:255'],
            'password' =>   ['required', 'string', 'max:255']
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken($user->name . ' Auth-Token')->plainTextToken;

        return response()->json([
            'message' => 'Login successfully realized',
            'token_type' => 'bearer',
            'token' => $token
        ]);
    }

    public function logout(Request $request): JsonResponse {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'logout completo'
        ]);
    }
}
