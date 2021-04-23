<?php

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use Exceptions;
use App\Exceptions\TooManyLoginAttemptsException;
use App\Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class UserService
{
    use AuthenticatesUsers,ThrottlesLogins;

    /**
     * @var $userRepository
     * @var $maxLoginAttempts
     * @var $lockoutTime
     */
    protected $userRepository;
    protected $maxLoginAttempts = 5;
    protected $lockoutTime = 30 * 60 ; // 30 * 60 seconds = 30 minutes

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
     * @return array $result
     */
    public function saveUserData(Request $requestData)
    {
        $this->validateRegisterData($requestData);

        $user = $this->userRepository->saveUser($requestData);

        $accessToken = $user->createToken('authToken')->accessToken;

        return [
            'user' => $user,
            'access_token' => $accessToken
        ];
    }

    public function loginUser(Request $requestData)
    {
        // Check number of login
        $this->check_number_of_login_attempts($requestData);
        // Validate login data
        $this->validateLoginData($requestData);
        // Get user from database
        // Attempt login (Remember to block user for 30 minutes if invalid login)
        if (!auth()->attempt($requestData->all())) {
            $this->incrementLoginAttempts($requestData);
            throw new InvalidCredentialsException("Invalid credentials");
        }
        return $this->return_successful_login_response();
        // Return login token?
    }


    /**
     * Validate array of register data and throws exception if data is invalid
     *
     * @param Illuminate\Http\Request $data
     */
    private function validateRegisterData(Request $data)
    {
        $validator = Validator::make($data->all(), [
            'name' => 'required|max:255',
            'password' => 'required|min:8',
            'email' => 'required|unique:users|max:255|email',
            'date_of_birth' => 'required|date',
            'image' => 'required|image|mimes:jpeg,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            $this->throw_JSON_invalid_argument_exception($validator->errors());
        }
    }


    /**
     * Validate array of login data and throws exception if data is invalid
     *
     * @param Illuminate\Http\Request $data
     */
    private function validateLoginData(Request $data)
    {
        $validator = Validator::make($data->all(), [
            'email' => 'required|max:255|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->throw_JSON_invalid_argument_exception($validator->errors());
        }
    }


    private function throw_JSON_invalid_argument_exception($errors)
    {
        $errorArray = json_encode($errors);
        throw new InvalidArgumentException($errorArray);
    }

    private function check_number_of_login_attempts(Request $request)
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            throw new TooManyLoginAttemptsException("Too many login attemps. You're account is locked for 30 minutes.");
        }
    }

    private function return_successful_login_response()
    {
        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return [
            'user' => auth()->user(),
            'access_token' => $accessToken
        ];
    }
}
