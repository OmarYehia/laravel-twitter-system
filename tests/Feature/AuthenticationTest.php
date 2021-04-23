<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_fields_for_registration()
    {
        $this->json('POST', 'api/users', ['Accept' => 'application/json'])
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

    public function test_password_length_constraint()
    {
        $userData = [
            "name" => "Omar Yehia",
            "email" => "example@example.com",
            "password" => 123456,
            "date_of_birth" => "1994-09-04",
            "image" => "image.jpg"
        ];

        $this->json('POST', 'api/users', $userData, ['Accept' => 'application/json'])
        ->assertStatus(400)
        ->assertJson([
            "status" => 400,
            "errors" => [
                "password" => ["The password must be at least 8 characters."],
            ]
        ]);
    }

    public function test_date_of_birth_date_constraint()
    {
        $userData = [
            "name" => "Omar Yehia",
            "email" => "example@example.com",
            "password" => 12345678,
            "date_of_birth" => "Bad Date",
            "image" => "image.jpg"
        ];

        $this->json('POST', 'api/users', $userData, ['Accept' => 'application/json'])
        ->assertStatus(400)
        ->assertJson([
            "status" => 400,
            "errors" => [
                "date_of_birth" => ["The date of birth is not a valid date."],
            ]
        ]);
    }
}
