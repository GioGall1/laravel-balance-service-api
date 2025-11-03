<?php

namespace App\Http\Controllers\Finance;

use App\DTO\DepositDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\DepositRequest;
use App\Services\Finance\DepositService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * Контроллер для обработки операций пополнения баланса пользователя.
 */
class DepositController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/deposit",
     *   summary="Пополнение баланса",
     *   tags={"Finance"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"user_id","amount"},
     *       @OA\Property(property="user_id", type="integer", example=1),
     *       @OA\Property(property="amount",  type="number", format="float", example=200.00),
     *       @OA\Property(property="comment", type="string", example="Покупка подписки")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Успех",
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="success"),
     *       @OA\Property(property="message", type="string", example="Средства успешно зачислены.")
     *     )
     *   )
     * )
     */
    public function __construct(private DepositService $service) {}

    public function store(DepositRequest $request): JsonResponse
    {

        $dto = new DepositDTO($request->validated());
        $this->service->deposit($dto);

        return response()->json([
            'status' => 'success',
            'message' => 'Средства успешно зачислены.',
            'data' => [
                'user_id' => $dto->user_id,
                'amount'  => number_format($dto->amount, 2, '.', ''),
                'comment' => $dto->comment,
            ],
        ], 200);
    }
}
