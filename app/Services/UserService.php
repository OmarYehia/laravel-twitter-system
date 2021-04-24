<?php

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use Exception;
use App\Exceptions\TooManyLoginAttemptsException;
use App\Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Traits\ThrowExceptionsTrait;
use App\Traits\ValidateAuthenticationTrait;
use App\Exceptions\UnauthenticatedUserException;
use App\Exceptions\NotFoundException;
use App\Exceptions\BadRequestException;

class UserService
{
    use AuthenticatesUsers,ThrottlesLogins, ThrowExceptionsTrait, ValidateAuthenticationTrait;

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
     * @param App\Contracts\UserRepositoryInterface $userRepository
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
            'user_id' => $user->id,
            'access_token' => $accessToken
        ];
    }

    public function loginUser(Request $requestData)
    {
        $this->check_number_of_login_attempts($requestData);

        $this->validateLoginData($requestData);

        $this->attempLogin($requestData);

        return $this->return_successful_login_response();
    }

    public function followUser(Request $requestData, $followee_id)
    {
        $user = $this->validateAuthentication();

        $followee = $this->validateIfFolloweeExists($followee_id);

        $this->validate_if_followee_is_already_followed($user, $followee_id);

        $this->userRepository->followUser($user, $followee);

        return ["message" => "User followed successfuly."];
    }

    private function validateIfFolloweeExists($followee_id)
    {
        $user = $this->userRepository->getUserById($followee_id);

        if (!$user) {
            throw new NotFoundException("Followee not found.");
        }

        if ($followee_id ==  auth()->guard('api')->user()->id) {
            throw new BadRequestException("Can't follow self.");
        }

        return $user;
    }

    private function validate_if_followee_is_already_followed($user, $followee_id)
    {
        $userFollowings = $this->userRepository->getUserFollowingsById($user->id);
        foreach ($userFollowings as $follower) {
            if ($follower->id == $followee_id) {
                throw new BadRequestException("Followee is already followed.");
            }
        }
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
            'access_token' => $accessToken
        ];
    }

    private function attempLogin(Request $request)
    {
        if (!auth()->attempt($request->all())) {
            $this->incrementLoginAttempts($request);
            throw new InvalidCredentialsException("Invalid credentials");
        }
    }
}
