<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Services\Finance\BalanceService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;


/**
 * Контроллер для получения текущего баланса пользователя.
 */
class BalanceController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/balance/{userId}",
     *   summary="Текущий баланс пользователя",
     *   tags={"Finance"},
     *   @OA\Parameter(
     *     name="userId", in="path", required=true, description="ID пользователя",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *     response=200, description="Ок",
     *     @OA\JsonContent(
     *       @OA\Property(property="user_id", type="integer", example=1),
     *       @OA\Property(property="balance", type="string", example="150.00")
     *     )
     *   )
     * )
     */
    public function show(int $userId, BalanceService $service): JsonResponse
    {
        $amount = $service->getBalance($userId);

        return response()->json([
            'user_id' => $userId,
            'balance' => $amount,
        ], 200);
    }
}