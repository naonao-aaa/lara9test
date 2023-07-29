<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostListControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function TOPページで、ブログ一覧が表示される()
    {
        $post1 = Post::factory()->create(['title' => 'ブログのタイトル1']);
        $post2 = Post::factory()->create(['title' => 'ブログのタイトル2']);

        $this->get('/')
            ->assertOk()
            ->assertSee('ブログのタイトル1')
            ->assertSee('ブログのタイトル2');
    }

    /** @test */
    function factoryの観察()
    {
        // $post = Post::factory()->make(['user_id' => null]); //1件データを作って、

        // dump($post);

        // dump(POST::get()->toArray());   //登録されているPostデータをgetして、配列にして、dumpで表示する。

        // dump(User::get()->toArray());   //また、同時に、Userデータをgetして、それを配列にして、dumpで表示する。

        $this->assertTrue(true);
    }
}
