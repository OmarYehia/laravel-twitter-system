<?php

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use Exceptions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class UserService
{
    /**
     * @var $userRepository
     */
    protected $userRepository;

    /**
     * UserService constructor
     *
     * @param App\Repositories\UserRepository $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Store data into DB
     *
     * @param Illuminate\Http\Request $requestData
     * @return App\Models\User $user
     */
    public function saveUserData($requestData)
    {
        $this->validateRequestData($requestData);

        $user = $this->userRepository->saveUser($requestData);
        return $user;
    }


    /**
     * Validate array of data and throws exception if data is invalid
     *
     * @param array $data
     */
    private function validateRequestData($data)
    {
        $validator = Validator::make($data->all(), [
            'name' => 'required|max:255',
            'password' => 'required|min:8',
            'email' => 'required|unique:users|max:255|email',
            'date_of_birth' => 'required|date',
            'image' => 'required|image|mimes:jpeg,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            $errorArray = json_encode($validator->errors());
            throw new InvalidArgumentException($errorArray);
        }
    }
}
