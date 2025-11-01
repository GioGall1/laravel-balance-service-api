<?php

namespace App\Services\Finance;

use App\Dto\WithdrawDto;
use App\Models\User;
use App\Models\Balance;
use App\Models\Transaction;
use App\Enums\TransactionType;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class WithdrawService
{
     public function withdraw(WithdrawDto $dto): void
    {
        DB::transaction(function () use ($dto) {
            $balance = Balance::firstOrCreate(
                ['user_id' => $dto->user_id],
                ['balance' => 0]
            );

            if ($balance->balance < $dto->amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Недостаточно средств на балансе.',
                ]);
            }

            $balance->balance -= $dto->amount;
            $balance->save();

            Transaction::create([
                'user_id' => $dto->user_id,
                'type'    => TransactionType::Withdraw,
                'amount'  => -$dto->amount,
                'comment' => $dto->comment,
            ]);
        });
    }
}