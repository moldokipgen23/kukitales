<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $posts = collect();
        if ($q !== '') {
            $posts = Post::published()
                ->where(function ($w) use ($q) {
                    $w->where('title', 'like', "%{$q}%")
                      ->orWhere('excerpt', 'like', "%{$q}%")
                      ->orWhere('content', 'like', "%{$q}%");
                })
                ->with(['user', 'category'])
                ->latest('published_at')->paginate(15)->withQueryString();
        }

        return view('search', ['q' => $q, 'posts' => $posts]);
    }
}
