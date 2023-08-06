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
        $this->post('mypage/posts/create', [])->assertRedirect($loginUrl);
        $this->get('mypage/posts/edit/1')->assertRedirect($loginUrl);
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

    /** @test */
    function マイページ、ブログを新規登録できる、公開の場合()
    {
        [$taro, $me, $jiro] = User::factory(3)->create();

        $this->login($me);

        $validData = [
            'title' => '私のブログタイトル',
            'body' => '私のブログ本文',
            'status' => '1',
        ];

        // $this->post('mypage/posts/create', $validData)
        //     ->assertRedirect('mypage/posts/edit/1'); // SQLiteのインメモリ

        $response = $this->post('mypage/posts/create', $validData);

        $post = Post::first();

        $response->assertRedirect('mypage/posts/edit/'.$post->id);

        $this->assertDatabaseHas('posts', array_merge($validData, ['user_id' => $me->id]));
    }

    /** @test */
    function マイページ、ブログを新規登録できる、非公開の場合()
    {
        [$taro, $me, $jiro] = User::factory(3)->create();

        $this->login($me);

        $validData = [
            'title' => '私のブログタイトル',
            'body' => '私のブログ本文',
            // 'status' => '1',
        ];

        $this->post('mypage/posts/create', $validData);

        $this->assertDatabaseHas('posts', array_merge($validData, [
            'user_id' => $me->id,
            'status' => 0,
        ]));
    }

    /** @test */
    function マイページ、ブログの登録時の入力チェック()
    {
        $url = 'mypage/posts/create';

        $this->login();

        $this->from($url)->post($url, [])
            ->assertRedirect($url);

        app()->setLocale('testing');

        $this->post($url, ['title' => ''])->assertInvalid(['title' => 'required']);
        $this->post($url, ['title' => str_repeat('a', 256)])->assertInvalid(['title' => 'max']);
        $this->post($url, ['title' => str_repeat('a', 255)])->assertValid('title');
        $this->post($url, ['body' => ''])->assertInvalid(['body' => 'required']);
    }

    /** @test */
    function 自分のブログの編集画面は開ける()
    {
        $post = Post::factory()->create();

        $this->login($post->user);

        $this->get('mypage/posts/edit/'.$post->id)
            ->assertOk();
    }

    /** @test */
    function 他人様のブログの編集画面は開けない()
    {
        $post = Post::factory()->create();

        $this->login();

        $this->get('mypage/posts/edit/'.$post->id)
            ->assertForbidden();
    }

    /** @test */
    function 他人様のブログは更新できない()
    {
    }

    /** @test */
    function 他人様のブログを削除はできない()
    {
    }
}
