<?php

namespace App\Exceptions;

use Exception;

class UnauthenticatedUserException extends Exception
{
    public function __construct($message = "Unauthorized for this action.")
    {
        parent::__construct($message);
    }
}
