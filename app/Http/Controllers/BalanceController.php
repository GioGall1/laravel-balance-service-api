<?php

namespace App\Http\Controllers;

use App\Services\BalanceService;
use Illuminate\Http\JsonResponse;

class BalanceController extends Controller
{
    public function show(int $userId, BalanceService $service): JsonResponse
    {
        $amount = $service->getBalance($userId);

        return response()->json([
            'user_id' => $userId,
            'balance' => $amount,
        ], 200);
    }
}