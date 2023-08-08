<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Middleware\PostShowLimit;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;
    // use WithoutMiddleware;

    /** @test */
    function TOPページで、ブログ一覧が表示される()
    {
        $post1 = Post::factory()->hasComments(3)->create(['title' => 'ブログのタイトル1']);
        $post2 = Post::factory()->hasComments(5)->create(['title' => 'ブログのタイトル2']);
        Post::factory()->hasComments(1)->create();
        
        $this->get('/')
            ->assertOk()
            ->assertSee('ブログのタイトル1')
            ->assertSee('ブログのタイトル2')
            ->assertSee($post1->user->name)
            ->assertSee($post2->user->name)
            ->assertSee('（3件のコメント）')
            ->assertSee('（5件のコメント）')
            ->assertSeeInOrder([
                '（5件のコメント）',
                '（3件のコメント）',
                '（1件のコメント）',
            ]);
    }

    /** @test */
    function ブログの一覧で、非公開のブログは表示されない()
    {
        $post1 = Post::factory()->closed()->create([
            'title' => 'これは非公開のブログです',
        ]);

        $post2 = Post::factory()->create([
            'title' => 'これは公開済みのブログです',
        ]);

        $this->get('/')
            ->assertDontSee('これは非公開のブログです')
            ->assertSee('これは公開済みのブログです');
    }

    /** @test */
    function ブログの詳細画面が表示でき、コメントが古い順に表示される()
    {
        // $this->withoutMiddleware(PostShowLimit::class);

        $post = Post::factory()->create();

        [$comment1, $comment2, $comment3] = Comment::factory()->createMany([
            ['created_at' => now()->sub('2 days'), 'name' => 'コメント太郎', 'post_id' => $post->id],
            ['created_at' => now()->sub('3 days'), 'name' => 'コメント次郎', 'post_id' => $post->id],
            ['created_at' => now()->sub('1 days'), 'name' => 'コメント三郎', 'post_id' => $post->id],
        ]);

        $this->get('posts/'.$post->id)
            ->assertOk()
            ->assertSee($post->title)
            ->assertSee($post->user->name)
            ->assertSeeInOrder(['コメント次郎', 'コメント太郎', 'コメント三郎']);
    }

    /** @test */
    function ブログで非公開のものは、詳細画面は表示できない()
    {
        $post = Post::factory()->closed()->create();

        $this->get('posts/'.$post->id)
            ->assertForbidden();
    }

    /** @test */
    function クリスマスの日は、メリークリスマス！と表示される()
    {
        $post = Post::factory()->create();

        Carbon::setTestNow('2020-12-24');

        $this->get('posts/'.$post->id)
            ->assertOk()
            ->assertDontSee('メリークリスマス！');

        Carbon::setTestNow('2020-12-25');

        $this->get('posts/'.$post->id)
            ->assertOk()
            ->assertSee('メリークリスマス！');
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
