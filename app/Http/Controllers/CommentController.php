<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'parent_id' => 'nullable|exists:comments,id',
            'content' => 'required|string|min:2|max:2000',
        ]);

        Comment::create([
            'post_id' => $data['post_id'],
            'parent_id' => $data['parent_id'] ?? null,
            'user_id' => auth()->id(),
            'content' => $data['content'],
            'status' => 'pending',
        ]);

        $post = Post::find($data['post_id']);

        return back()->with('success', 'Comment submitted — pending moderation.');
    }
}
