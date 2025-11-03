<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\TransferRequest;
use Illuminate\Http\JsonResponse;
use App\DTO\TransferDto;
use App\Services\Finance\TransferService;

/**
 * Контроллер для обработки операций перевода баланса пользователю.
 */
class TransferController extends Controller
{
    public function __construct(private readonly TransferService $service) {}

    public function store(TransferRequest $request): JsonResponse
    {

        $dto = new TransferDTO($request->validated());
        $result = $this->service->handle($dto);

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