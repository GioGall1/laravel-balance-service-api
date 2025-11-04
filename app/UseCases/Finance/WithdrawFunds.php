<?php

namespace App\UseCases\Finance;

use App\DTO\WithdrawDTO;
use App\Enums\TransactionType;
use App\Services\Finance\BalanceLedger;
use Illuminate\Support\Facades\DB;

final class WithdrawFunds
{
    public function __construct(private readonly BalanceLedger $ledger) {}

    public function handle(WithdrawDTO $dto): void
    {
        DB::transaction(function () use ($dto) {
            $this->ledger->debit(
                userId:   $dto->user_id,
                amount:   $dto->amount,
                comment:  $dto->comment,
                type:     TransactionType::Withdraw
            );
        });
    }
}