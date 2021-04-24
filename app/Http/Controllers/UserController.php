<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\UserService;
use Exception;
use InvalidArgumentException;
use App\Exceptions\TooManyLoginAttemptsException;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UnauthenticatedUserException;
use App\Exceptions\NotFoundException;
use App\Traits\ErrorsTrait;
use App\Traits\SuccessResponseTrait;
use App\Exceptions\BadRequestException;

class UserController extends Controller
{
    use ErrorsTrait, SuccessResponseTrait;

    /**
     * @var userService
     */
    protected $userService;


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
            $result = $this->set_status_and_success_message(Response::HTTP_CREATED, $serviceResponse);
        } catch (InvalidArgumentException $exception) {
            $errors = json_decode($exception->getMessage(), true);
            $result = $this->set_status_and_error_message(Response::HTTP_BAD_REQUEST, $errors);
        } catch (Exception $exception) {
            $errors = $exception->getMessage();
            $result =  $this->set_status_and_error_message(Response::HTTP_INTERNAL_SERVER_ERROR, $errors);
        }

        return response()->json($result['response'], $result['status']);
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
            $result = $this->set_status_and_success_message(Response::HTTP_OK, $serviceResponse);
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

        return response()->json($result['response'], $result['status']);
    }


    public function follow(Request $request, $followee_id)
    {
        $result = [];

        try {
            $serviceResponse = $this->userService->followUser($request, $followee_id);
            $result = $this->set_status_and_success_message(Response::HTTP_OK, $serviceResponse);
        } catch (UnauthenticatedUserException $exception) {
            $errors = $exception->getMessage();
            $result =  $this->set_status_and_error_message(Response::HTTP_UNAUTHORIZED, $errors);
        } catch (NotFoundException $exception) {
            $errors = $exception->getMessage();
            $result =  $this->set_status_and_error_message(Response::HTTP_NOT_FOUND, $errors);
        } catch (BadRequestException $exception) {
            $errors = $exception->getMessage();
            $result =  $this->set_status_and_error_message(Response::HTTP_BAD_REQUEST, $errors);
        } catch (Exception $exception) {
            $errors = $exception->getMessage();
            $result =  $this->set_status_and_error_message(Response::HTTP_INTERNAL_SERVER_ERROR, $errors);
        }

        return response()->json($result['response'], $result['status']);
    }
}
