<?php

namespace App\Services\Finance;

use App\DTO\DepositDTO;
use App\Models\Balance;
use App\Models\Transaction;
use App\Enums\TransactionType;
use App\Exceptions\UserNotFoundException;
use Illuminate\Support\Facades\DB;

class DepositService
{
    public function deposit(DepositDTO $dto): void
    {
        DB::transaction(function () use ($dto) {
            $balance = Balance::firstOrCreate(
                ['user_id' => $dto->user_id],
                ['balance' => 0]
            );

            $balance->balance += $dto->amount;
            $balance->save();

            Transaction::create([
                'user_id' => $dto->user_id,
                'type' => TransactionType::Deposit,
                'amount' => $dto->amount,
                'comment' => $dto->comment,
            ]);
        });
    }
}