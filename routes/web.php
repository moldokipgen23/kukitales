<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'index'])->name('search');

// SEO endpoints
Route::get('/robots.txt',            [SeoController::class, 'robots'])->name('seo.robots');
Route::get('/sitemap.xml',           [SeoController::class, 'sitemapIndex'])->name('seo.sitemap');
Route::get('/sitemap-posts.xml',     [SeoController::class, 'sitemapPosts'])->name('seo.sitemap.posts');
Route::get('/sitemap-categories.xml',[SeoController::class, 'sitemapCategories'])->name('seo.sitemap.categories');
Route::get('/sitemap-series.xml',    [SeoController::class, 'sitemapSeries'])->name('seo.sitemap.series');
Route::get('/sitemap-news.xml',      [SeoController::class, 'sitemapNews'])->name('seo.sitemap.news');
Route::get('/feed.xml',              [SeoController::class, 'rss'])->name('seo.rss');
Route::get('/rss',                   [SeoController::class, 'rss']);

// Ad click tracking
Route::get('/ads/{advertisement}/click', [AdvertisementController::class, 'click'])->name('ads.click');

// Donations
Route::get('/donate',           [DonationController::class, 'index'])->name('donate.index');
Route::get('/donate/{slug}',    [DonationController::class, 'show'])->name('donate.show');
Route::post('/donate/{slug}',   [DonationController::class, 'store'])->name('donate.store');

Route::get('/author/{id}', [AuthorController::class, 'show'])->name('authors.show');

Route::get('/series/{slug}', [SeriesController::class, 'show'])->name('series.show');
Route::get('/series/{series}/{slug}', [PostController::class, 'episode'])->name('posts.episode');

Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('categories.show');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);

    // Social sign-in
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->whereIn('provider', ['google', 'facebook'])
        ->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->whereIn('provider', ['google', 'facebook'])
        ->name('social.callback');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard',  [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/bookmarks',  [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks/{post}', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    Route::get('/submit',     [SubmissionController::class, 'create'])->name('submit.create');
    Route::post('/submit',    [SubmissionController::class, 'store'])->name('submit.store');
    Route::post('/comments',  [CommentController::class, 'store'])->name('comments.store');
});

// Catch-all post URL — must be last
Route::get('/{type}/{slug}', [PostController::class, 'show'])
    ->where('type', 'story|history|news|folktale|blog|music|gallery')
    ->name('posts.show');
