<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Tweet;
use Tests\Traits\CreateFakeUserTrait;

class TweetingTest extends TestCase
{
    use RefreshDatabase, CreateFakeUserTrait;

    public function test_required_fields_while_adding_tweet()
    {
        $user = $this->createFakeUser('sample@example.com');
        
        $this->actingAs($user, 'api')->json('POST', 'api/tweets', ['Accept' => 'application/json'])
        ->assertStatus(400)
        ->assertJson([
            "success" => false,
            "errors" => [
                "text" => ["The text field is required."],
            ]
        ]);
    }

    public function test_deny_tweets_bigger_than_140_characters()
    {
        $tweet = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. In hendrerit lacus in sapien dapibus fermentum. Quisque in auctor ante orci aliquam.";

        $user = $this->createFakeUser('sample@example.com');

        $this->actingAs($user, 'api')->json('POST', 'api/tweets', ['text' => $tweet], ['Accept' => 'application/json'])
        ->assertStatus(400)
        ->assertJson([
            "success" => false,
            "errors" => [
                "text" => ["The text must not be greater than 140 characters."],
            ]
        ]);
    }

    public function test_succesfully_posting_tweet()
    {
        $tweet = "Lorem ipsum dolor sit amet";

        $user = $this->createFakeUser('sample@example.com');

        $this->actingAs($user, 'api')->json('POST', 'api/tweets', ['text' => $tweet], ['Accept' => 'application/json'])
        ->assertStatus(201)
        ->assertJson([
            "success" => true,
            "data" => [
                "message" => "Tweet posted successfuly."
            ]
        ]);
    }
}
