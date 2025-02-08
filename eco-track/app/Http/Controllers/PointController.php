<?php

namespace App\Http\Controllers;

use App\Models\Point;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointController extends Controller
{
    //

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
