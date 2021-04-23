<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Exception;
use InvalidArgumentException;

class UserController extends Controller
{
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
    public function store(Request $request)
    {
        $result = [];

        try {
            $result['data'] = $this->userService->saveUserData($request);
            $result['status'] = 201;
        } catch (InvalidArgumentException $exception) {
            $errors = json_decode($exception->getMessage(), true);
            $result = $this->set_status_and_error_message(400, $errors);
        } catch (Exception $exception) {
            $errors = $exception->getMessage();
            $result =  $this->set_status_and_error_message(500, $errors);
        }

        return response()->json($result, $result['status']);
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
