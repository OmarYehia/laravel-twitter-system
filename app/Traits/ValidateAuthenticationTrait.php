<?php
namespace App\Traits;

use App\Exceptions\UnauthenticatedUserException;

trait ValidateAuthenticationTrait
{
    /**
    * Validate if the user is authenticated or not
    * throws UnauthenticatedUserException if failed
    *
    * @return App\Models\User
    * @throws App\Exceptions\UnauthenticatedUserException
    */
    private function getAuthenticatedUser()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            throw new UnauthenticatedUserException();
        }
        
        return $user;
    }
}
