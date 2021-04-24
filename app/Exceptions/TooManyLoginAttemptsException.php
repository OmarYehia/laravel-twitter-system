<?php

namespace App\Exceptions;

use Exception;

class TooManyLoginAttemptsException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
