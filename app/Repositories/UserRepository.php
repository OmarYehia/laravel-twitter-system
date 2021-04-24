<?php

namespace App\Repositories;

use App\Models\User;
use App\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Traits\UploadTrait;

class UserRepository implements UserRepositoryInterface
{
    use UploadTrait;

    /**
     * @var $user
     */
    protected $user;


    /**
     * UserRepository constructor
     *
     * @param App\Models\User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }


    /**
     * Saves the user into database
     *
     * @param Illuminate\Http\Request $requestData
     * @return App\Models\User
     */
    public function saveUser($requestData)
    {
        $user = new $this->user;

        $user->name = $requestData->name;
        $user->password = Hash::make($requestData->password);
        $user->email = $requestData->email;
        $user->date_of_birth = $requestData->date_of_birth;
        $user->image = $this->uploadImageToServer($requestData);

        try {
            $user->save();
        } catch (QueryException $e) {
            throw new Exception('Database is currently down.');
        }

        return $user->fresh();
    }

    public function getUserById($id)
    {
        return User::find($id);
    }

    public function getUserFollowingsById($id)
    {
        return User::find($id)->followings()->get();
    }

    public function followUser($user, $followee)
    {
        return $user->followings()->attach($followee);
    }

    public function getAllUsersWithTweets()
    {
        return User::with(['tweets'])->get();
    }
}
