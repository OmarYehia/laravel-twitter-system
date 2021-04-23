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

        $user->save();

        return $user->fresh();
    }

    /**
     * Uploads image to /public/uploads/images
     *
     * @param Illuminate\Http\Request $requestData
     * @return String $filePath a file path to be saved in DB
     */
    private function uploadImageToServer($requestData)
    {
        $image = $requestData->file('image');
        $nameSlug = Str::slug($requestData->name) . "_" . time();
        $folder = '/uploads/images/';
        $filePath = $folder . $nameSlug . "." . $image->getClientOriginalExtension();
        $file = $this->uploadOne($image, $folder, 'public', $nameSlug);
                
        return $filePath;
    }
}
