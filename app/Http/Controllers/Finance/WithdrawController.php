<?php

namespace App\Http\Controllers\Finance;

use App\Dto\WithdrawDto;
use App\Http\Requests\Finance\WithdrawRequest;
use App\Http\Controllers\Controller;
use App\Services\Finance\WithdrawService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * Контроллер для обработки операций списания баланса пользователя.
 */
class WithdrawController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/withdraw",
     *   summary="Списание средств с баланса пользователя",
     *   tags={"Finance"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"user_id","amount"},
     *       @OA\Property(property="user_id", type="integer", example=1, description="ID пользователя"),
     *       @OA\Property(property="amount",  type="number", format="float", example=100.00, description="Сумма списания"),
     *       @OA\Property(property="comment", type="string", example="Оплата услуг")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Успешное списание средств",
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="success"),
     *       @OA\Property(property="message", type="string", example="Списание средств успешно выполнено")
     *     )
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Недостаточно средств или некорректные данные",
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Недостаточно средств на балансе")
     *     )
     *   )
     * )
     */
   public function __construct(private WithdrawService $service) {}

    public function store(WithdrawRequest $request): JsonResponse
    {

        $dto = new WithdrawDTO($request->validated());
        $this->service->withdraw($dto);

        return response()->json([
            'status'  => 'success',
            'message' => 'Списание средств успешно выполнено',
        ]);
    }
}