<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface TweetRepositoryInterface
{
    public function saveTweet(Request $request);
}
