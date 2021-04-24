<?php
namespace App\Traits;

trait SuccessResponseTrait
{
    /**
    * Returns a formatted error message
    *
    * @param Integer $status_code
    * @param String $message
    * @return array associative array containing 'status' and 'response'
    */
    private function set_status_and_success_message($status_code, $message)
    {
        return [
            'status' => $status_code,
            'response' => [
                "success" => true,
                "data" => $message
            ]
        ];
    }
}
