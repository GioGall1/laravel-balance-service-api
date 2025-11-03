<?php

namespace App\Services\Finance;

use App\DTO\TransferDto;
use App\Enums\TransactionType;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\UserNotFoundException;
use App\Models\Transaction;
use App\Models\Balance;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Сервис перевода средств между пользователями.
 */
final class TransferService
{
    public function handle(TransferDto $dto): void
    {
       DB::transaction(function () use ($dto) {

            $existingIds = DB::table('users')
                ->whereIn('id', [$dto->from_user_id, $dto->to_user_id])
                ->pluck('id')
                ->all();

            if (!in_array($dto->from_user_id, $existingIds, true) || !in_array($dto->to_user_id, $existingIds, true)) {
                throw new UserNotFoundException('Один из пользователей не найден.');
            }

            if ($dto->from_user_id === $dto->to_user_id) {
                throw new RuntimeException('Нельзя переводить самому себе.');
            }

            Balance::firstOrCreate(['user_id' => $dto->from_user_id], ['balance' => 0]);
            Balance::firstOrCreate(['user_id' => $dto->to_user_id], ['balance' => 0]);

            $debited = Balance::where('user_id', $dto->from_user_id)
                ->where('balance', '>=', $dto->amount)
                ->decrement('balance', $dto->amount);

            if ($debited === 0) {
                throw new InsufficientFundsException('Недостаточно средств.');
            }

            Balance::where('user_id', $dto->to_user_id)
                ->increment('balance', $dto->amount);

            Transaction::create([
                'user_id'         => $dto->from_user_id,
                'related_user_id' => $dto->to_user_id,
                'type'            => TransactionType::TransferOut->value,
                'amount'          => $dto->amount,
                'comment'         => $dto->comment,
            ]);

            Transaction::create([
                'user_id'         => $dto->to_user_id,
                'related_user_id' => $dto->from_user_id,
                'type'            => TransactionType::TransferIn->value,
                'amount'          => $dto->amount,
                'comment'         => $dto->comment,
            ]);
        });
    }
}