<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('submissions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:story,episode,history,folktale,news,blog,music,gallery',
            'category_id' => 'nullable|exists:categories,id',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string|min:50',
            'cover_image' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        Post::create([
            ...$data,
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Submission received — awaiting editor review.');
    }
}
