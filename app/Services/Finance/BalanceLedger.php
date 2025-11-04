<?php

namespace App\Services\Finance;

use App\Enums\TransactionType;
use App\Exceptions\InsufficientFundsException;
use App\Models\Balance;
use App\Models\Transaction;

final class BalanceLedger
{
    /**
     * Зачисляет средства на счёт пользователя.
     */
    public function credit(int $userId, float $amount, ?string $comment = null): void
    {
        Balance::firstOrCreate(['user_id' => $userId], ['balance' => 0]);

        Balance::where('user_id', $userId)->increment('balance', $amount);

        Transaction::create([
            'user_id' => $userId,
            'type'    => TransactionType::Deposit->value,
            'amount'  => $amount,
            'comment' => $comment,
        ]);
    }

    /**
     * Списывает средства со счёта пользователя.
     */
    public function debit(
        int $userId,
        float $amount,
        ?string $comment = null,
        TransactionType $type = TransactionType::Withdraw
    ): void {
        $balance = Balance::firstOrCreate(['user_id' => $userId], ['balance' => 0]);

        if ($balance->balance < $amount) {
            throw new InsufficientFundsException('Недостаточно средств.');
        }

        Balance::where('user_id', $userId)->decrement('balance', $amount);

        Transaction::create([
            'user_id' => $userId,
            'type'    => $type->value,
            'amount'  => $amount,
            'comment' => $comment,
        ]);
    }
}