<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Post extends Model
{
    use HasSlug;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'type', 'status',
        'user_id', 'category_id', 'series_id', 'episode_number',
        'cover_image', 'audio_file', 'year_era', 'source',
        'is_featured', 'is_breaking', 'allow_tts',
        'view_count', 'read_time', 'meta_title', 'meta_description',
        'focus_keyword', 'og_image', 'canonical_url', 'noindex',
        'published_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_breaking' => 'boolean',
        'allow_tts'   => 'boolean',
        'noindex'     => 'boolean',
        'published_at' => 'datetime',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug');
    }

    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            if ($post->content) {
                $words = str_word_count(strip_tags($post->content));
                $post->read_time = max(1, (int) ceil($words / 200));
            }
            if ($post->status === 'published' && ! $post->published_at) {
                $post->published_at = now();
            }
        });

        // Bust sitemap/RSS cache + ping search engines on publish
        static::saved(function (Post $post) {
            \Illuminate\Support\Facades\Cache::forget('sitemap.posts');
            \Illuminate\Support\Facades\Cache::forget('sitemap.news');
            \Illuminate\Support\Facades\Cache::forget('rss.feed');

            $becamePublished = $post->wasChanged('status') && $post->status === 'published';
            $autoPing = (bool) \App\Models\SiteSetting::get('auto_ping_search_engines', '1');

            if ($becamePublished && $autoPing && ! app()->environment('local')) {
                static::pingSearchEngines();
            }
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('sitemap.posts');
            \Illuminate\Support\Facades\Cache::forget('sitemap.news');
            \Illuminate\Support\Facades\Cache::forget('rss.feed');
        });
    }

    protected static function pingSearchEngines(): void
    {
        $sitemap = urlencode(route('seo.sitemap'));
        $urls = [
            "https://www.google.com/ping?sitemap={$sitemap}",
            "https://www.bing.com/ping?sitemap={$sitemap}",
        ];
        foreach ($urls as $url) {
            try {
                \Illuminate\Support\Facades\Http::timeout(4)->get($url);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Sitemap ping failed', ['url' => $url, 'error' => $e->getMessage()]);
            }
        }
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function series(): BelongsTo { return $this->belongsTo(Series::class); }
    public function comments(): HasMany { return $this->hasMany(Comment::class); }
    public function approvedComments(): HasMany { return $this->hasMany(Comment::class)->where('status', 'approved')->whereNull('parent_id'); }
    public function bookmarks(): HasMany { return $this->hasMany(Bookmark::class); }
    public function postViews(): HasMany { return $this->hasMany(PostView::class); }
    public function media(): HasMany { return $this->hasMany(Media::class); }
    public function tags(): BelongsToMany { return $this->belongsToMany(Tag::class, 'post_tag'); }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published')->where(function ($q) {
            $q->whereNull('published_at')->orWhere('published_at', '<=', now());
        });
    }

    public function scopeFeatured(Builder $q): Builder { return $q->where('is_featured', true); }
    public function scopeBreaking(Builder $q): Builder { return $q->where('is_breaking', true); }
    public function scopeOfType(Builder $q, string $type): Builder { return $q->where('type', $type); }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    }

    public function getUrlAttribute(): string
    {
        if ($this->series_id && $this->series) {
            return route('posts.episode', ['series' => $this->series->slug, 'slug' => $this->slug]);
        }
        return route('posts.show', ['type' => $this->type, 'slug' => $this->slug]);
    }

    public function getShortExcerptAttribute(): string
    {
        return $this->excerpt ?: Str::limit(strip_tags($this->content), 140);
    }

    /* ── SEO helpers ───────────────────────────────────────────── */

    public function getSeoTitleAttribute(): string
    {
        $siteName = \App\Models\SiteSetting::get('site_name', 'KukiTales');
        return ($this->meta_title ?: $this->title) . ' — ' . $siteName;
    }

    public function getSeoDescriptionAttribute(): string
    {
        return $this->meta_description
            ?: ($this->excerpt ?: Str::limit(strip_tags($this->content), 155));
    }

    public function getOgImageUrlAttribute(): ?string
    {
        if ($this->og_image) {
            return str_starts_with($this->og_image, 'http')
                ? $this->og_image
                : asset('storage/' . $this->og_image);
        }
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        $fallback = \App\Models\SiteSetting::get('default_og_image');
        if ($fallback) {
            return str_starts_with($fallback, 'http') ? $fallback : asset('storage/' . $fallback);
        }
        return null;
    }

    public function getCanonicalAttribute(): string
    {
        return $this->canonical_url ?: $this->url;
    }
}
