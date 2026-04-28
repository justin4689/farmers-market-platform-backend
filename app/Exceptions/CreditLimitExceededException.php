<?php

namespace App\Exceptions;

use RuntimeException;

class CreditLimitExceededException extends RuntimeException
{
    public function __construct(int $available)
    {
        parent::__construct("Credit limit exceeded. Available: {$available} FCFA");
    }
}
