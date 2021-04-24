<?php

namespace App\Services;

use App\Contracts\TweetRepositoryInterface;
use Illuminate\Http\Request;
use App\Exceptions\UnauthenticatedUserException;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use App\Traits\ThrowExceptionsTrait;

class TweetService
{
    use ThrowExceptionsTrait;

    /**
     * @var $tweetRepository
     */
    protected $tweetRepository;

    /**
     * TweetService constructor
     *
     * @param App\Contracts\TweetRepositoryInterface $tweetRepository
     */
    public function __construct(TweetRepositoryInterface $tweetRepository)
    {
        $this->tweetRepository = $tweetRepository;
    }

    public function saveTweetData(Request $request)
    {
        $this->validateAuthentication();
        $this->validateTweetData($request);

        $tweet = $this->tweetRepository->saveTweet($request);

        return $tweet;
    }


    private function validateAuthentication()
    {
        $user = auth()->guard('api')->user();
        if (!$user) {
            throw new UnauthenticatedUserException("Unauthorized for this action.");
        }
    }

    private function validateTweetData(Request $data)
    {
        $validator = Validator::make($data->all(), [
            'text' => 'required|max:140',
        ]);

        if ($validator->fails()) {
            $this->throw_JSON_invalid_argument_exception($validator->errors());
        }
    }
}
