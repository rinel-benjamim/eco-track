<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Autenticação",
 *     description="Endpoints para autenticação de usuários"
 * )
 */

class AuthController extends Controller
{
    /**
     * Registrar um novo usuário
     *
     * @OA\Post(
     *     path="/register",
     *     summary="Registrar um usuário",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Usuário registrado com sucesso"),
     *     @OA\Response(response=400, description="Dados inválidos")
     * )
     */

    public function register(Request $request): JsonResponse
    {

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

    /**
     * Login do usuário
     *
     * @OA\Post(
     *     path="/login",
     *     summary="Fazer login",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login bem-sucedido"),
     *     @OA\Response(response=401, description="Credenciais inválidas")
     * )
     */
    public function login(Request $request): JsonResponse
    {

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

    /**
     * Logout do usuário autenticado
     *
     * @OA\Post(
     *     path="/logout",
     *     summary="Fazer logout",
     *     security={{"sanctum":{}}},
     *     tags={"Autenticação"},
     *     @OA\Response(response=200, description="Logout bem-sucedido"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'logout completo'
        ]);
    }
}
