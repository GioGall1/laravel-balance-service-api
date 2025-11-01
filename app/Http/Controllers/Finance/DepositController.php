<?php

namespace App\Http\Controllers\Finance;

use App\DTO\DepositDTO;
use App\Http\Controllers\Controller;
use App\Services\Finance\DepositService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DepositController extends Controller
{
    public function __construct(private DepositService $service) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'amount'  => ['required', 'numeric', 'min:0.01'],
            'comment' => ['nullable', 'string', 'max:255'],
        ]);

        $dto = new DepositDTO($data);
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
