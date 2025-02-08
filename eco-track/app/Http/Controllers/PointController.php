<?php

namespace App\Http\Controllers;

use App\Models\Point;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Pontuação",
 *     description="Gerenciamento da pontuação dos usuários"
 * )
 */

class PointController extends Controller
{
    
    /**
     * Obter o ranking dos 10 usuários com mais pontos
     *
     * @OA\Get(
     *     path="/ranking",
     *     summary="Obter ranking de pontuação",
     *     tags={"Pontuação"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Top 10 usuários com mais pontos"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function index(): JsonResponse
    {
        // Buscar os usuários ordenados pelos pontos em ordem decrescente
        $ranking = Point::with('user')
            ->orderByDesc('points')
            ->limit(10) // Limita aos 10 melhores
            ->get();

        return response()->json([
            'message' => 'Top 10 melhores contribuintes',
            'users' => $ranking
        ], 200);
    }

      /**
     * Obter a pontuação do usuário autenticado
     *
     * @OA\Get(
     *     path="/me/points",
     *     summary="Obter minha pontuação",
     *     tags={"Pontuação"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Retorna a pontuação do usuário autenticado"),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Usuário não possui pontos")
     * )
     */
    public function myPoints(): JsonResponse
    {
        // Buscar os pontos do usuário autenticado
        $points = Point::where('user_id', Auth::id())->sum('points');


        if (!$points) {
            return response()->json([
                'message' => 'Você não possui nenhum ponto'
            ]);
        }

        return response()->json([
            'user_id' => Auth::id(),
            'points' => $points
        ], 200);
    }
}
