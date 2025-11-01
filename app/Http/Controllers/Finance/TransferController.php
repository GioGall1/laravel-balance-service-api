<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\DTO\TransferDto;
use App\Services\Finance\TransferService;

class TransferController extends Controller
{
    public function __construct(private readonly TransferService $service)
    {
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'from_user_id' => 'required|integer|different:to_user_id|exists:users,id',
            'to_user_id'   => 'required|integer|exists:users,id',
            'amount'       => 'required|numeric|gt:0',
            'comment'      => 'nullable|string|max:255',
        ]);

        $dto = new TransferDto($data);
        $result = $this->service->handle($dto);

        return response()->json($result, 201);
    }
}