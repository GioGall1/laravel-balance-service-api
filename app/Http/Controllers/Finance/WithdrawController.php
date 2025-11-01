<?php

namespace App\Http\Controllers\Finance;

use App\Dto\WithdrawDto;
use App\Http\Controllers\Controller;
use App\Services\Finance\WithdrawService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WithdrawController extends Controller
{
   public function __construct(private WithdrawService $service) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'amount'  => 'required|numeric|min:0.01',
            'comment' => 'nullable|string|max:255',
        ]);

        $dto = new WithdrawDTO($validated);

        $this->service->withdraw($dto);

        return response()->json([
            'status'  => 'success',
            'message' => 'Списание средств успешно выполнено',
        ]);
    }
}