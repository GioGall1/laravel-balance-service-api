<?php

namespace App\Services\Finance;

use App\DTO\TransferDto;
use App\Enums\TransactionType;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\UserNotFoundException;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Balance;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TransferService
{
    public function handle(TransferDto $dto): array
    {
        return DB::transaction(function () use ($dto) {

            $fromUser = User::query()->find($dto->from_user_id);
            $toUser   = User::query()->find($dto->to_user_id);

            if (!$fromUser || !$toUser) {
                throw new UserNotFoundException('Один из пользователей не найден.');
            }
            if ($fromUser->id === $toUser->id) {
                throw new RuntimeException('Нельзя переводить самому себе.');
            }

            $ids = [$fromUser->id, $toUser->id];
            sort($ids);

            $balances = Balance::query()
                ->whereIn('user_id', $ids)
                ->lockForUpdate()
                ->get()
                ->keyBy('user_id');

            foreach ($ids as $uid) {
                if (!isset($balances[$uid])) {
                    $balances[$uid] = Balance::query()->create([
                        'user_id' => $uid,
                        'balance' => 0,
                    ]);
                }
            }

            $fromBal = $balances[$fromUser->id];
            $toBal   = $balances[$toUser->id];

            $amountCents = (int) round($dto->amount * 100);
            if ($amountCents <= 0) {
                throw new RuntimeException('Сумма перевода должна быть больше нуля.');
            }

            $fromCents = (int) round(((float)$fromBal->balance) * 100);
            $toCents   = (int) round(((float)$toBal->balance)   * 100);

            if ($fromCents < $amountCents) {
                throw new InsufficientFundsException('Недостаточно средств.');
            }

            $fromCents -= $amountCents;
            $toCents   += $amountCents;

            $fromBal->balance = $fromCents / 100;
            $fromBal->save();

            $toBal->balance = $toCents / 100;
            $toBal->save();

            $outTx = Transaction::query()->create([
                'user_id'         => $fromUser->id,
                'related_user_id' => $toUser->id,
                'type'            => TransactionType::TransferOut->value,
                'amount'          => $dto->amount,
                'comment'         => $dto->comment,
            ]);

            $inTx = Transaction::query()->create([
                'user_id'         => $toUser->id,
                'related_user_id' => $fromUser->id,
                'type'            => TransactionType::TransferIn->value,
                'amount'          => $dto->amount,
                'comment'         => $dto->comment,
            ]);

            return [
                'success' => true,
                'data' => [
                    'from_user' => [
                        'id'      => $fromUser->id,
                        'balance' => (float)$fromBal->balance,
                        'transaction_id' => $outTx->id,
                    ],
                    'to_user' => [
                        'id'      => $toUser->id,
                        'balance' => (float)$toBal->balance,
                        'transaction_id' => $inTx->id,
                    ],
                    'amount'  => (float)$dto->amount,
                    'comment' => $dto->comment,
                ],
            ];
        });
    }
}