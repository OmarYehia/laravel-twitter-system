<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Tweet;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_fields_for_registration()
    {
        $this->json('POST', 'api/register', ['Accept' => 'application/json'])
        ->assertStatus(400)
        ->assertJson([
            "success" => false,
            "errors" => [
                "name" => ["The name field is required."],
                "password" => ["The password field is required."],
                "email" => ["The email field is required."],
                "date_of_birth" => ["The date of birth field is required."],
                "image" => ["The image field is required."]
            ]
        ]);
    }

    public function test_too_short_password_length_constraint()
    {
        $userData = [
            "name" => "Omar Yehia",
            "email" => "example@example.com",
            "password" => 123456,
            "date_of_birth" => "1994-09-04",
            "image" => "image.jpg"
        ];

        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
        ->assertStatus(400)
        ->assertJson([
            "success" => false,
            "errors" => [
                "password" => ["The password must be at least 8 characters."],
            ]
        ]);
    }

    public function test_invalid_date_of_birth_date_constraint()
    {
        $userData = [
            "name" => "Omar Yehia",
            "email" => "example@example.com",
            "password" => 12345678,
            "date_of_birth" => "Bad Date",
            "image" => "image.jpg"
        ];

        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(400)
            ->assertJson([
                "success" => false,
                "errors" => [
                    "date_of_birth" => ["The date of birth is not a valid date."],
                ]
            ]);
    }

    public function test_successful_registeration()
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('file.jpg');

        $userData = [
            "name" => "Omar Yehia",
            "email" => "example@example.com",
            "password" => 12345678,
            "date_of_birth" => "1994-09-04",
            "image" => $file
        ];

        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJsonStructure([
                "success",
                "data" => [
                    "user_id",
                    "access_token",
                ],
            ]);
    }

    public function test_login_must_enter_email_and_password()
    {
        $this->json('POST', 'api/login')
            ->assertStatus(400)
            ->assertJson([
                "success" => false,
                "errors" => [
                    "email" => ["The email field is required."],
                    "password" => ["The password field is required."],
                ]
            ]);
    }

    public function test_account_is_locked_after_too_many_login_attempts()
    {
        Storage::fake('local');
        $image = UploadedFile::fake()->create('file.jpg');

        $user = User::factory()->create([
            'email' => 'sample@example.com',
            'password' => Hash::make('testpassword'),
            'image' => $image
        ]);

        $loginData = ['email' => 'sample@example.com', 'password' => 'wrongpassword'];

        for ($i = 0; $i < 5; $i++) {
            $this->json('POST', 'api/login', $loginData, ['Accept' => 'application/json']);
        }

        $this->json('POST', 'api/login', $loginData, ['Accept' => 'application/json'])
            ->assertStatus(403)
            ->assertJson([
                "success" => false,
                "errors" => "Too many login attemps. You're account is locked for 30 minutes."
            ]);
    }

    public function test_successful_login()
    {
        Storage::fake('local');
        $image = UploadedFile::fake()->create('file.jpg');

        $user = User::factory()->create([
            'email' => 'sample@example.com',
            'password' => Hash::make('testpassword'),
            'image' => $image
        ]);

        $loginData = ['email' => 'sample@example.com', 'password' => 'testpassword'];

        $this->json('POST', 'api/login', $loginData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                "success",
                "data" => [
                    "access_token",
                ],
            ]);
    }
}
