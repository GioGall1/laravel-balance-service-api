<?php

namespace App\DTO;

class DepositDTO
{
    public int $user_id;
    public float $amount;
    public ?string $comment;

    public function __construct(array $data)
    {
        $this->user_id = (int) $data['user_id'];
        $this->amount = (float) $data['amount'];
        $this->comment = $data['comment'] ?? null;
    }
}