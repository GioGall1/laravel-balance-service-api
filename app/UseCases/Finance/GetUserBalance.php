<?php

namespace App\UseCases\Finance;

use App\Exceptions\UserNotFoundException;
use App\Models\Balance;
use App\Models\User;

class GetUserBalance
{
    public function handle(int $userId): string
    {
        $user = User::query()->find($userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $balance = Balance::query()
            ->where('user_id', $userId)
            ->value('balance');

        return $balance !== null ? number_format((float)$balance, 2, '.', '') : '0.00';
    }
}