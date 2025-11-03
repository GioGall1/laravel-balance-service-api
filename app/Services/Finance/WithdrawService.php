<?php

namespace App\Services\Finance;

use App\Dto\WithdrawDto;
use App\Models\Balance;
use App\Models\Transaction;
use App\Enums\TransactionType;
use App\Exceptions\InsufficientFundsException;
use Illuminate\Support\Facades\DB;

/**
 * Сервис списания средств пользователя.
 */
class WithdrawService
{
     public function withdraw(WithdrawDto $dto): void
    {
        DB::transaction(function () use ($dto) {
           Balance::firstOrCreate(
                ['user_id' => $dto->user_id],
                ['balance' => 0]
            );

            $updated = Balance::where('user_id', $dto->user_id)
                ->where('balance', '>=', $dto->amount)
                ->decrement('balance', $dto->amount);


             if ($updated === 0) {
                throw new InsufficientFundsException('Недостаточно средств на балансе.');
            }

            Transaction::create([
                'user_id' => $dto->user_id,
                'type'    => TransactionType::Withdraw->value,
                'amount'  => -$dto->amount,
                'comment' => $dto->comment,
            ]);
        });
    }
}