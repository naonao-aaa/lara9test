<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostManageController extends Controller
{
    public function index()
    {
        // $posts = Post::where('user_id', auth()->user()->id)->get();
        $posts = auth()->user()->posts;

        return view('mypage.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('mypage.posts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'max:255'],
            'body' => ['required'],
            'status' => ['nullable', 'boolean'],
        ]);

        $data['status'] = $request->boolean('status');

        $post = auth()->user()->posts()->create($data);

        return redirect('mypage/posts/edit/'.$post->id);
    }

    public function edit(Post $post)
    {
        if (auth()->user()->isNot($post->user)) {
            abort(403);
        }

        $data = old() ?? $post;

        return view('mypage.posts.edit', compact('data'));
    }

    public function update(Post $post, Request $request)
    {
        if (auth()->user()->isNot($post->user)) {
            abort(403);
        }

        $data = $request->validate([
            'title' => ['required', 'max:255'],
            'body' => ['required'],
            'status' => ['nullable', 'boolean'],
        ]);

        $data['status'] = $request->boolean('status');

        $post->update($data);

        return redirect(route('mypage.posts.edit', $post))
            ->with('status', 'ブログを更新しました');
    }

    public function destroy(Post $post)
    {
        if (auth()->user()->isNot($post->user)) {
            abort(403);
        }

        $post->delete(); // 今回は、このブログに紐づくコメントは、DBの制約を使って削除することにした。（2023_07_30_112030_create_comments_table.php で制約を付ける）

        return redirect('mypage/posts');
    }
}
