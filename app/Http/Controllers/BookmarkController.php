<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Post;

class BookmarkController extends Controller
{
    public function index()
    {
        $posts = auth()->user()->bookmarks()
            ->with('post.user', 'post.category')
            ->latest()->paginate(12);
        return view('user.bookmarks', compact('posts'));
    }

    public function toggle(Post $post)
    {
        $userId = auth()->id();
        $existing = Bookmark::where('user_id', $userId)->where('post_id', $post->id)->first();
        if ($existing) {
            $existing->delete();
            $message = 'Bookmark removed.';
        } else {
            Bookmark::create(['user_id' => $userId, 'post_id' => $post->id]);
            $message = 'Bookmarked.';
        }
        return back()->with('success', $message);
    }
}
