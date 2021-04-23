<?php

namespace App\Exceptions;

use Exception;

class InvalidCredentialsException extends Exception
{
    public function __concstruct($message)
    {
        parent::__concstruct($message);
    }
}
