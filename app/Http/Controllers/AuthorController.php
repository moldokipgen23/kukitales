<?php

namespace App\Http\Controllers;

use App\Models\User;

class AuthorController extends Controller
{
    public function show(int $id)
    {
        $author = User::findOrFail($id);
        $posts = $author->posts()->where('status', 'published')->latest('published_at')->paginate(12);
        return view('authors.show', compact('author', 'posts'));
    }
}
