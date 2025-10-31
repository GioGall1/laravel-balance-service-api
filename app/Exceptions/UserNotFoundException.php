<?php

namespace App\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{
    public function __construct(string $message = 'Пользователь не найден')
    {
        parent::__construct($message, 404);
    }
}