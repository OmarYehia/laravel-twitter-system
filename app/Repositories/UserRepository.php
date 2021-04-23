<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Traits\UploadTrait;

class UserRepository
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
     * Saves the post into database
     *
     * @param array $data
     * @return App\Models\User
     */
    public function save($data)
    {
        $user = new $this->user;

        // $user->name = $data['name'];
        // $user->password = Hash::make($data['password']);
        // $user->email = $data['email'];
        // $user->date_of_birth = $data['date_of_birth'];
        $user->name = $data->name;
        $user->password = Hash::make($data->password);
        $user->email = $data->email;
        $user->date_of_birth = $data->date_of_birth;
        $user->image = $this->uploadImageToServer($data);

        $user->save();

        return $user->fresh();
    }

    /**
     * Uploads image to /public/uploads/images
     *
     * @param Illuminate\Http\Request $data
     * @return String $filePath a file path to be saved in DB
     */
    private function uploadImageToServer($data)
    {
        $image = $data->file('image');
        $nameSlug = Str::slug($data->name) . "_" . time();
        $folder = '/uploads/images/';
        $filePath = $folder . $nameSlug . "." . $image->getClientOriginalExtension();
        $file = $this->uploadOne($image, $folder, 'public', $nameSlug);
                
        return $filePath;
    }
}
