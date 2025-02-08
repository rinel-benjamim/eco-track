<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Point;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


/**
 * @OA\Tag(
 *     name="Atividades",
 *     description="Gerenciamento das atividades sustentáveis dos usuários"
 * )
 */

class ActivityController extends Controller
{
     /**
     * Listar todas as atividades do usuário autenticado
     *
     * @OA\Get(
     *     path="/activities",
     *     summary="Listar atividades",
     *     security={{"sanctum":{}}},
     *     tags={"Atividades"},
     *     @OA\Response(response=200, description="Lista de atividades do usuário autenticado"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function index(): JsonResponse
    {
        $activities = Activity::where('user_id', Auth::id())->get();

        return response()->json([
            'success' => true,
            'message' => 'Atividades recuperadas com sucesso.',
            'data' => $activities
        ], 200);
    }

     /**
     * Criar uma nova atividade
     *
     * @OA\Post(
     *     path="/activities",
     *     summary="Criar uma nova atividade",
     *     security={{"sanctum":{}}},
     *     tags={"Atividades"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"description", "points", "title", "category"},
     *             @OA\Property(property="description", type="string", maxLength=500, example="Participei de uma campanha de reciclagem"),
     *             @OA\Property(property="points", type="integer", minimum=1, example=10),
     *             @OA\Property(property="title", type="string", maxLength=255, example="Reciclagem de Plástico"),
     *             @OA\Property(property="category", type="string", maxLength=255, example="Reciclagem")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Atividade criada com sucesso"),
     *     @OA\Response(response=422, description="Dados inválidos"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'description' => 'required|string|max:500',
            'points' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255'
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
     * Obter detalhes de uma atividade específica
     *
     * @OA\Get(
     *     path="/activities/{id}",
     *     summary="Obter detalhes de uma atividade",
     *     security={{"sanctum":{}}},
     *     tags={"Atividades"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="ID da atividade"
     *     ),
     *     @OA\Response(response=200, description="Atividade encontrada"),
     *     @OA\Response(response=404, description="Atividade não encontrada"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
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
     * Excluir uma atividade
     *
     * @OA\Delete(
     *     path="/activities/{id}",
     *     summary="Excluir atividade",
     *     security={{"sanctum":{}}},
     *     tags={"Atividades"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="ID da atividade"
     *     ),
     *     @OA\Response(response=200, description="Atividade excluída com sucesso"),
     *     @OA\Response(response=404, description="Atividade não encontrada"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
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

    /**
     * Atualizar uma atividade existente
     *
     * @OA\Put(
     *     path="/activities/{id}",
     *     summary="Atualizar atividade",
     *     security={{"sanctum":{}}},
     *     tags={"Atividades"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="ID da atividade"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="description", type="string", maxLength=500, example="Nova descrição"),
     *             @OA\Property(property="points", type="integer", minimum=1, example=15),
     *             @OA\Property(property="title", type="string", maxLength=255, example="Título atualizado"),
     *             @OA\Property(property="category", type="string", example="Energia")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Atividade atualizada com sucesso"),
     *     @OA\Response(response=404, description="Atividade não encontrada"),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=422, description="Dados inválidos")
     * )
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'description' => 'sometimes|string|max:500',
            'points' => 'sometimes|integer|min:1',
            'title' => 'sometimes|string|max:255',
            'category' => 'sometimes|in:Reciclagem,Energia,Água,Mobilidade'
        ]);

        // Buscar a atividade do usuário autenticado
        $activity = Activity::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$activity) {
            return response()->json(['message' => 'Atividade não encontrada'], 404);
        }

        // Verifica se pontos foram enviados na requisição
        if ($request->has('points')) {
            $oldPoints = $activity->points;
            $newPoints = $request->points;

            if ($oldPoints !== $newPoints) { // Só atualizar se os valores forem diferentes
                $userPoints = Point::where('user_id', Auth::id())->first();

                if ($userPoints) {
                    $userPoints->points += ($newPoints - $oldPoints);
                    $userPoints->save();
                }

                $activity->points = $newPoints;
            }
        }

        // Atualizar os demais campos (evita update vazio)
        $activity->fill($request->only(['description', 'title', 'category']));

        if ($activity->isDirty()) { // Só salva se houver mudanças
            $activity->save();
        }

        return response()->json([
            'message' => 'Atividade atualizada com sucesso!',
            'activity' => $activity
        ], 200);
    }
}
