<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Point;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $activities = Activity::where('user_id', Auth::id())->get();

        return response()->json($activities, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'description' => 'required|string|max:500',
            'points' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'category'=> 'required|string|max:255'
        ]);

        // Criar a atividade
        $activity = Activity::create([
            'description' => $request->description,
            'points' => $request->points,
            'title' => $request->title,
            'category' => $request->category,
            'user_id' => Auth::id()
        ]);

        // Atualizar ou criar a pontuação do usuário
        $userPoints = Point::where('user_id', Auth::id())->first();

        if ($userPoints) {
            // Se já existe um registro, somamos os pontos
            $userPoints->increment('points', $request->points);
        } else {
            // Caso contrário, criamos um novo registro de pontos
            Point::create([
                'user_id' => Auth::id(),
                'points' => $request->points
            ]);
        }

        return response()->json([
            'message' => 'Atividade criada com sucesso!',
            'activity' => $activity
        ], 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $activity = Activity::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('user')
            ->first();

        if (!$activity) {
            return response()->json(['message' => 'Atividade não encontrada'], 404);
        }

        return response()->json($activity, 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        // Buscar a atividade do usuário autenticado
        $activity = Activity::where('id', $id)
            ->where('user_id', Auth::id()) // Garante que o usuário só pode excluir suas atividades
            ->first();

        if (!$activity) {
            return response()->json(['message' => 'Atividade não encontrada'], 404);
        }

        $activity->delete();

        return response()->json(['message' => 'Atividade excluída com sucesso'], 200);
    }
}
