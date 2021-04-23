<?php

namespace App\Exceptions;

use Exception;

class TooManyLoginAttemptsException extends Exception
{
    public function __concstruct($message)
    {
        parent::__concstruct($message);
    }
}
