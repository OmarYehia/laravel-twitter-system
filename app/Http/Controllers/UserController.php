<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\UserService;
use Exception;
use InvalidArgumentException;
use App\Exceptions\TooManyLoginAttemptsException;
use App\Exceptions\InvalidCredentialsException;

class UserController extends Controller
{
    /**
     * @var userService
     */
    protected $userService;
    private $_cookieExpirationDuration = 60 * 24 * 3; // 60 minutes * 24 hours * 3 i.e 3 days
    /**
     * UserController constructor
     *
     * @param App\Services\UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Stores newly created user in storage
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $result = [];

        try {
            $serviceResponse = $this->userService->saveUserData($request);
            $result['user'] = $serviceResponse['user'];
            $result['status'] = Response::HTTP_CREATED;
        } catch (InvalidArgumentException $exception) {
            $errors = json_decode($exception->getMessage(), true);
            $result = $this->set_status_and_error_message(Response::HTTP_BAD_REQUEST, $errors);
        } catch (Exception $exception) {
            $errors = $exception->getMessage();
            $result =  $this->set_status_and_error_message(Response::HTTP_INTERNAL_SERVER_ERROR, $errors);
        }

        return response()->json($result, $result['status'])
        ->cookie('access_token', isset($serviceResponse['access_token']) ? $serviceResponse['access_token'] : null, $this->_cookieExpirationDuration);
    }

    /**
     * Logs in the user
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $result = [];
        try {
            $serviceResponse = $this->userService->loginUser($request);
            $result['status'] = Response::HTTP_OK;
            $result['user'] = $serviceResponse['user'];
        } catch (InvalidCredentialsException $exception) {
            $errors = $exception->getMessage();
            $result =  $this->set_status_and_error_message(Response::HTTP_UNAUTHORIZED, $errors);
        } catch (TooManyLoginAttemptsException $exception) {
            $errors = $exception->getMessage();
            $result =  $this->set_status_and_error_message(Response::HTTP_FORBIDDEN, $errors);
        } catch (InvalidArgumentException $exception) {
            $errors = json_decode($exception->getMessage(), true);
            $result = $this->set_status_and_error_message(Response::HTTP_BAD_REQUEST, $errors);
        } catch (Exception $exception) {
            $errors = $exception->getMessage();
            $result =  $this->set_status_and_error_message(Response::HTTP_INTERNAL_SERVER_ERROR, $errors);
        }

        return response()->json($result, $result['status'])
        ->cookie('access_token', isset($serviceResponse['access_token']) ? $serviceResponse['access_token'] : null, $this->_cookieExpirationDuration);
    }

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
