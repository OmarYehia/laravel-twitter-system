<?php
namespace Tests\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait CreateFakeUserTrait
{
    private function createFakeUser($email)
    {
        Storage::fake('local');
        $image = UploadedFile::fake()->create('file.jpg');

        $user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make('testpassword'),
            'image' => $image
        ]);

        return $user;
    }
}
