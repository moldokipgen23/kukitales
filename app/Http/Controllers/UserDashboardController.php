<?php

namespace App\Http\Controllers;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $posts = $user->posts()->latest()->limit(20)->get();
        $bookmarks = $user->bookmarks()->with('post')->latest()->limit(20)->get();
        return view('user.dashboard', compact('user', 'posts', 'bookmarks'));
    }
}
