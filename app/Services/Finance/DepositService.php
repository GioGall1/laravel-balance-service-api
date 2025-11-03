<?php

namespace App\Services\Finance;

use App\DTO\DepositDTO;
use App\Models\Balance;
use App\Models\Transaction;
use App\Enums\TransactionType;
use Illuminate\Support\Facades\DB;

/**
 * Сервис пополнения средств пользователя.
 */
final class DepositService
{
    public function deposit(DepositDTO $dto): void
    {
        DB::transaction(function () use ($dto) {
            Balance::firstOrCreate(
                ['user_id' => $dto->user_id],
                ['balance' => 0]
            );

            Balance::where('user_id', $dto->user_id)
                ->increment('balance', $dto->amount);

            Transaction::create([
                'user_id' => $dto->user_id,
                'type' => TransactionType::Deposit->value,
                'amount' => $dto->amount,
                'comment' => $dto->comment,
            ]);
        });
    }
}