<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface UserRepositoryInterface
{
    public function saveUser(Request $request);

    public function getUserById($id);

    public function getUserFollowingsById($id);
    
    public function followUser($user, $followee);

    public function getAllUsersWithTweets();
}
