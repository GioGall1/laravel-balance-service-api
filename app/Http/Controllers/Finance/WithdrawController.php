<?php

namespace App\Http\Controllers\Finance;

use App\Dto\WithdrawDto;
use App\Http\Requests\Finance\WithdrawRequest;
use App\Http\Controllers\Controller;
use App\Services\Finance\WithdrawService;
use Illuminate\Http\JsonResponse;

/**
 * Контроллер для обработки операций списания баланса пользователя.
 */

class WithdrawController extends Controller
{
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