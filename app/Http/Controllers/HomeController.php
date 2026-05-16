<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Series;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Post::published()->featured()
            ->whereIn('type', ['story', 'history'])
            ->latest('published_at')->first()
            ?? Post::published()->whereIn('type', ['story', 'history'])->latest('published_at')->first();

        $stories = Post::published()->ofType('story')
            ->when($featured, fn ($q) => $q->where('id', '!=', $featured->id))
            ->latest('published_at')->limit(3)->get();

        $seriesList = Series::with(['posts' => fn ($q) => $q->latest('published_at')->limit(1)])
            ->withCount('posts')
            ->latest()->limit(3)->get();

        $history = Post::published()->ofType('history')->latest('published_at')->limit(4)->get();

        $folktaleCategories = Category::where('type', 'folktale')
            ->whereNotNull('parent_id')
            ->orderBy('sort_order')->limit(8)->get();

        $news = Post::published()->ofType('news')->latest('published_at')->limit(6)->get();

        $blogs = Post::published()->ofType('blog')->with('user')->latest('published_at')->limit(3)->get();

        $breaking = Post::published()->breaking()->latest('published_at')->limit(5)->pluck('title');

        $newsCategories = Category::where('type', 'news')->orderBy('sort_order')->get();

        return view('home', compact(
            'featured', 'stories', 'seriesList', 'history',
            'folktaleCategories', 'news', 'blogs', 'breaking', 'newsCategories'
        ));
    }
}
