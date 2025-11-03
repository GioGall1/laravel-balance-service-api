<?php

namespace App\Http\Controllers\Finance;

use App\DTO\DepositDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\DepositRequest;
use App\Services\Finance\DepositService;
use Illuminate\Http\JsonResponse;

/**
 * Контроллер для обработки операций пополнения баланса пользователя.
 */

class DepositController extends Controller
{
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
