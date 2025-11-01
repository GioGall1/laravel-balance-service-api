<?php

namespace App\DTO;

class TransferDto
{
    public int $from_user_id;
    public int $to_user_id;
    public float $amount;
    public ?string $comment;

    public function __construct(array $data)
    {
        $this->from_user_id = (int)($data['from_user_id'] ?? 0);
        $this->to_user_id   = (int)($data['to_user_id'] ?? 0);
        $this->amount       = (float)($data['amount'] ?? 0);
        $this->comment      = $data['comment'] ?? null;
    }
}