<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Usuários",
 *     description="Gerenciamento de usuários"
 * )
 */

class UserController extends Controller
{
    /**
     * Obter detalhes do usuário autenticado
     *
     * @OA\Get(
     *     path="/me",
     *     summary="Obter informações do usuário autenticado",
     *     security={{"sanctum":{}}},
     *     tags={"Usuários"},
     *     @OA\Response(response=200, description="Dados do usuário autenticado"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function me(): JsonResponse
    {
        return response()->json(Auth::user());
    }

    /**
     * Listar todos os usuários
     *
     * @OA\Get(
     *     path="/users",
     *     summary="Listar usuários",
     *     security={{"sanctum":{}}},
     *     tags={"Usuários"},
     *     @OA\Response(response=200, description="Lista de usuários retornada com sucesso"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function index(): JsonResponse
    {
        $users = User::all();
        return response()->json($users);
    }
    
    /**
     * Exibir um usuário específico
     *
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Obter um usuário específico",
     *     security={{"sanctum":{}}},
     *     tags={"Usuários"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID do usuário"
     *     ),
     *     @OA\Response(response=200, description="Usuário encontrado"),
     *     @OA\Response(response=404, description="Usuário não encontrado"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        return response()->json($user);
    }
}
