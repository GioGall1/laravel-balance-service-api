<?php

namespace App\Exceptions;

use Exception;

class InsufficientFundsException extends Exception
{
    public function __construct(string $message = 'Недостаточно средств')
    {
        parent::__construct($message, 409);
    }
}