<?php
namespace App\Traits;

trait ErrorsTrait
{
    /**
    * Returns a formatted error message
    *
    * @param Integer $status_code
    * @param String $message
    * @return array associative array containing 'status' and 'error'
    */
    private function set_status_and_error_message($status_code, $message)
    {
        return [
            'status' => $status_code,
            'errors' => $message
        ];
    }
}
