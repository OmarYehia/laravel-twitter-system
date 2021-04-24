<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Tweet;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Tests\Traits\CreateFakeUserTrait;

class FollowersTest extends TestCase
{
    use RefreshDatabase, CreateFakeUserTrait;

    public function test_deny_unauthenticated_user_follow_request()
    {
        $user = $this->createFakeUser('sample@example.com');
        
        $this->json('POST', 'api/v1/friendships/2', ['Accept' => 'application/json'])
        ->assertStatus(401)
        ->assertJson([
            "success" => false,
            "errors" => "Unauthorized for this action."
        ]);
    }

    public function test_user_can_not_follow_himself()
    {
        $user = $this->createFakeUser('sample@example.com');
        
        $this->actingAs($user, 'api')->json('POST', 'api/v1/friendships/' . $user->id, ['Accept' => 'application/json'])
        ->assertStatus(400)
        ->assertJson([
            "success" => false,
            "errors" => "Can't follow self."
        ]);
    }

    public function test_user_can_not_follow_another_same_user_again()
    {
        $user = $this->createFakeUser('sample@example.com');
        $user2 = $this->createFakeUser('sample2@example.com');

        $this->actingAs($user, 'api')->json('POST', 'api/v1/friendships/' . $user2->id, ['Accept' => 'application/json']);
        
        $this->actingAs($user, 'api')->json('POST', 'api/v1/friendships/' . $user2->id, ['Accept' => 'application/json'])
        ->assertStatus(400)
        ->assertJson([
            "success" => false,
            "errors" => "Followee is already followed."
        ]);
    }

    public function test_successful_user_follow()
    {
        $user = $this->createFakeUser('sample@example.com');
        $user2 = $this->createFakeUser('sample2@example.com');

        $this->actingAs($user, 'api')->json('POST', 'api/v1/friendships/' . $user2->id, ['Accept' => 'application/json'])
        ->assertStatus(200)
        ->assertJson([
            "success" => true,
            "data" => ["message" => "User followed successfuly."]
        ]);
    }
}
