<?php

namespace App\UseCases\Finance;

use App\DTO\DepositDTO;
use App\Services\Finance\BalanceLedger;
use Illuminate\Support\Facades\DB;

final class DepositFunds
{
    public function __construct(private readonly BalanceLedger $ledger) {}

    public function execute(DepositDTO $dto): void
    {
        DB::transaction(function () use ($dto) {
            $this->ledger->credit($dto->user_id, $dto->amount, $dto->comment);
        });
    }
}