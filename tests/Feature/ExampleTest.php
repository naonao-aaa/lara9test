<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        // 準備
        $user = User::factory()->create();
        dump($user->id);

        // 実行
        $response = $this->get('/');

        // 検証
        $response->assertStatus(200);
    }

    public function test_the_application_returns_a_successful_response2()
    {
        // 準備
        $user = User::factory()->create();
        dump($user->id);

        // 実行
        $response = $this->get('/');

        // 検証
        $response->assertStatus(200);
    }

}
