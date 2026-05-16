<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Series;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function show(Request $request, string $type, string $slug)
    {
        $post = Post::published()
            ->ofType($type)
            ->where('slug', $slug)
            ->with(['user', 'category', 'tags', 'series'])
            ->firstOrFail();

        return $this->render($request, $post);
    }

    public function episode(Request $request, string $series, string $slug)
    {
        $seriesModel = Series::where('slug', $series)->firstOrFail();
        $post = Post::published()
            ->where('series_id', $seriesModel->id)
            ->where('slug', $slug)
            ->with(['user', 'category', 'tags', 'series'])
            ->firstOrFail();

        return $this->render($request, $post);
    }

    protected function render(Request $request, Post $post)
    {
        $post->increment('view_count');
        $post->postViews()->create([
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
        ]);

        $related = Post::published()
            ->where('id', '!=', $post->id)
            ->where(function ($q) use ($post) {
                $q->where('category_id', $post->category_id)
                  ->orWhere('type', $post->type);
            })
            ->latest('published_at')->limit(3)->get();

        $comments = $post->approvedComments()->with(['user', 'replies.user'])->latest()->get();

        return view('posts.show', compact('post', 'related', 'comments'));
    }
}
