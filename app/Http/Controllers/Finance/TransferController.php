<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\TransferRequest;
use Illuminate\Http\JsonResponse;
use App\DTO\TransferDto;
use App\Services\Finance\TransferService;
use OpenApi\Annotations as OA;

/**
 * Контроллер для обработки операций перевода баланса пользователю.
 */
class TransferController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/transfer",
     *   summary="Перевод средств между пользователями",
     *   tags={"Finance"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"from_user_id","to_user_id","amount"},
     *       @OA\Property(property="from_user_id", type="integer", example=1, description="ID отправителя"),
     *       @OA\Property(property="to_user_id",   type="integer", example=2, description="ID получателя"),
     *       @OA\Property(property="amount", type="number", format="float", example=50.00, description="Сумма перевода"),
     *       @OA\Property(property="comment", type="string", example="Перевод другу")
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Перевод успешно выполнен",
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="success"),
     *       @OA\Property(property="message", type="string", example="Перевод выполнен."),
     *       @OA\Property(property="data", type="object",
     *         @OA\Property(property="from_user_id", type="integer", example=1),
     *         @OA\Property(property="to_user_id", type="integer", example=2),
     *         @OA\Property(property="amount", type="string", example="50.00"),
     *         @OA\Property(property="comment", type="string", example="Перевод другу")
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Ошибка перевода (например, недостаточно средств или одинаковые пользователи)"
     *   )
     * )
     */
    public function __construct(private readonly TransferService $service) {}

    public function store(TransferRequest $request): JsonResponse
    {

        $dto = new TransferDTO($request->validated());
        $this->service->handle($dto);

        return response()->json([
        'status'  => 'success',
        'message' => 'Перевод выполнен.',
        'data'    => [
            'from_user_id' => $dto->from_user_id,
            'to_user_id'   => $dto->to_user_id,
            'amount'       => number_format($dto->amount, 2, '.', ''),
            'comment'      => $dto->comment,
        ],
        ], 201);
    }
}