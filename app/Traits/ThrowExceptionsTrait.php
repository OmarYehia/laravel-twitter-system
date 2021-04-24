<?php
namespace App\Traits;

use InvalidArgumentException;

trait ThrowExceptionsTrait
{
    /**
    * Throw JSON encoded errors array in an InvalidArgumentException
    *
    */
    private function throw_JSON_invalid_argument_exception($errors)
    {
        $errorArray = json_encode($errors);
        throw new InvalidArgumentException($errorArray);
    }
}
