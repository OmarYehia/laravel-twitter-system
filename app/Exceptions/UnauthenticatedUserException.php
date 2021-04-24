<?php

namespace App\Exceptions;

use Exception;

class UnauthenticatedUserException extends Exception
{
    public function __concstruct($message)
    {
        parent::__concstruct($message);
    }
}
