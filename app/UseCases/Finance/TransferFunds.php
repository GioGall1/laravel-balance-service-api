<?php

namespace App\UseCases\Finance;

use App\DTO\TransferDTO;
use App\Exceptions\UserNotFoundException;
use App\Services\Finance\BalanceLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\DB as FacadesDB;
use RuntimeException;

final class TransferFunds
{
    public function __construct(private readonly BalanceLedger $ledger) {}

    public function execute(TransferDTO $dto): void
    {
        if ($dto->from_user_id === $dto->to_user_id) {
            throw new RuntimeException('Нельзя переводить самому себе.');
        }

        DB::transaction(function () use ($dto) {
            $existing = FacadesDB::table('users')
                ->whereIn('id', [$dto->from_user_id, $dto->to_user_id])
                ->pluck('id')
                ->all();

            if (!in_array($dto->from_user_id, $existing, true) || !in_array($dto->to_user_id, $existing, true)) {
                throw new UserNotFoundException('Один из пользователей не найден.');
            }

            $this->ledger->debit($dto->from_user_id, $dto->amount, $dto->comment);
            $this->ledger->credit($dto->to_user_id,   $dto->amount, $dto->comment);
        });
    }
}