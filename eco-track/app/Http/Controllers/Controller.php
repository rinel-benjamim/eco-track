<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="EcoTrack API",
 *     version="1.0",
 *     description="API para monitoramento de atividades sustentáveis.",
 *     @OA\Contact(
 *         email="suporte@ecotrack.com",
 *         name="Suporte EcoTrack"
 *     )
 * )
 */

abstract class Controller
{
    /**
     * @OA\Get(
     *     path="/ods/categories",
     *     summary="Listar categorias ODS",
     *     security={{"sanctum":{}}},
     *     tags={"Categorias"},
     *     @OA\Response(response=200, description="Lista de categorias ODS retornada com sucesso"),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
}
