<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;

class CategoryController extends Controller
{
    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = Post::published()
            ->where(function ($q) use ($category) {
                $q->where('category_id', $category->id)
                  ->orWhereIn('category_id', $category->children()->pluck('id'));
            })
            ->with(['user', 'category'])
            ->latest('published_at')->paginate(12);

        return view('categories.show', compact('category', 'posts'));
    }
}
