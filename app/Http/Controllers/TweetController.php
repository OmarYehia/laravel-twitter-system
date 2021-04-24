<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\TweetService;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\UnauthenticatedUserException;
use InvalidArgumentException;
use App\Traits\ErrorsTrait;
use App\Traits\SuccessResponseTrait;

class TweetController extends Controller
{
    use ErrorsTrait, SuccessResponseTrait;
    
    /**
     * @var tweetService
     */
    protected $tweetService;
    /**
     * UserController constructor
     *
     * @param App\Services\TweetService $tweetService
     */
    public function __construct(TweetService $tweetService)
    {
        $this->tweetService = $tweetService;
    }


    public function store(Request $request)
    {
        $result = [];

        try {
            $serviceResponse = $this->tweetService->saveTweetData($request);
            $result = $this->set_status_and_success_message(Response::HTTP_CREATED, $serviceResponse);
        } catch (InvalidArgumentException $exception) {
            $errors = json_decode($exception->getMessage(), true);
            $result = $this->set_status_and_error_message(Response::HTTP_BAD_REQUEST, $errors);
        } catch (UnauthenticatedUserException $exception) {
            $errors = $exception->getMessage();
            $result = $this->set_status_and_error_message(Response::HTTP_UNAUTHORIZED, $errors);
        } catch (Exception $exception) {
            $errors = $exception->getMessage();
            $result =  $this->set_status_and_error_message(Response::HTTP_INTERNAL_SERVER_ERROR, $errors);
        }

        return response()->json($result['response'], $result['status']);
    }
}
