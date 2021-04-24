<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\TweetService;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\UnauthenticatedUserException;
use InvalidArgumentException;
use App\Traits\ErrorsTrait;

class TweetController extends Controller
{
    use ErrorsTrait;
    
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
            $result['tweet'] = $serviceResponse;
            $result['status'] = Response::HTTP_CREATED;
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

        return response()->json($result, $result['status']);
    }
}
