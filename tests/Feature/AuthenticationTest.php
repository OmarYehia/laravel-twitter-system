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
            "status" => 400,
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
            "status" => 400,
            "errors" => [
                "password" => ["The password must be at least 8 characters."],
            ]
        ]);
    }

    public function test_inavalid_date_of_birth_date_constraint()
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
                "status" => 400,
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
                "status",
                "user" => [
                    "id",
                    "name",
                    "email",
                    "date_of_birth",
                    "image",
                    "created_at",
                    "updated_at"
                ],
            ])
            ->assertCookie('access_token');
    }

    public function test_login_must_enter_email_and_password()
    {
        $this->json('POST', 'api/login')
            ->assertStatus(400)
            ->assertJson([
                "status" => 400,
                "errors" => [
                    "email" => ["The email field is required."],
                    "password" => ["The password field is required."],
                ]
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
               "user" => [
                   'id',
                   'name',
                   'email',
                   'created_at',
                   'updated_at',
               ],
                "status",
            ])
            ->assertCookie('access_token');
    }

    public function test_missing_text_while_adding_tweet()
    {
        Storage::fake('local');
        $image = UploadedFile::fake()->create('file.jpg');

        $user = User::factory()->create([
            'email' => 'sample@example.com',
            'password' => Hash::make('testpassword'),
            'image' => $image
        ]);
        
        $this->actingAs($user, 'api')->json('POST', 'api/tweets', ['Accept' => 'application/json'])
        ->assertStatus(400)
        ->assertJson([
            "status" => 400,
            "errors" => [
                "text" => ["The text field is required."],
            ]
        ]);
    }

    public function test_tweets_bigger_than_140_characters()
    {
        $tweet = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In hendrerit lacus in sapien dapibus fermentum. Quisque in auctor ante orci aliquam.";

        Storage::fake('local');
        $image = UploadedFile::fake()->create('file.jpg');

        $user = User::factory()->create([
            'email' => 'sample@example.com',
            'password' => Hash::make('testpassword'),
            'image' => $image
        ]);

        $this->actingAs($user, 'api')->json('POST', 'api/tweets', ['text' => $tweet], ['Accept' => 'application/json'])
        ->assertStatus(400)
        ->assertJson([
            "status" => 400,
            "errors" => [
                "text" => ["The text must not be greater than 140 characters."],
            ]
        ]);
    }
}
