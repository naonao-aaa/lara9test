<?php

namespace Tests\Feature\Http\Controllers\Mypage;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostManageControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function ゲストはブログを管理できない()
    {
        $loginUrl = 'mypage/login';

        $this->get('mypage/posts')->assertRedirect($loginUrl);
        $this->get('mypage/posts/create')->assertRedirect($loginUrl);
    }

    /** @test */
    function マイページ、ブログ一覧で自分のデータのみ表示される()
    {
      // 認証済みの場合

        $user = $this->login();

        $other = Post::factory()->create();
        $mypost = Post::factory()->create(['user_id' => $user->id]);

        $this->get('mypage/posts')
            ->assertOk()
            ->assertDontSee($other->title)
            ->assertSee($mypost->title);
    }

    /** @test */
    function マイページ、ブログの新規登録画面を開ける()
    {
        $this->login();

        $this->get('mypage/posts/create')
            ->assertOk();
    }
}