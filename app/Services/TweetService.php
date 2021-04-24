<?php

namespace App\Services;

use App\Contracts\TweetRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ThrowExceptionsTrait;
use App\Traits\ValidateAuthenticationTrait;

class TweetService
{
    use ThrowExceptionsTrait, ValidateAuthenticationTrait;

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

        return [
            "message" => "Tweet posted successfuly."
        ];
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
