<?php

namespace App\Repositories;

use App\Contracts\TweetRepositoryInterface;
use App\Models\Tweet;

class TweetRepository implements TweetRepositoryInterface
{
    /**
     * @var $tweet
     */
    protected $tweet;


    /**
     * TweetRepository constructor
     *
     * @param App\Models\Tweet $tweet
     */
    public function __construct(Tweet $tweet)
    {
        $this->tweet = $tweet;
    }

    /**
     * Saves the tweet into database
     *
     * @param Illuminate\Http\Request $requestData
     * @return App\Models\Tweet
     */
    public function saveTweet($requestData)
    {
        $tweet = new $this->tweet;

        $tweet->text = $requestData->text;
        $tweet->user_id = auth()->guard('api')->user()->id;

        $tweet->save();

        return $tweet->fresh();
    }
}
