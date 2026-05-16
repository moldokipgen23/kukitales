<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Series extends Model
{
    use HasSlug;

    protected $table = 'series';

    protected $fillable = [
        'title', 'slug', 'description', 'cover_image',
        'user_id', 'category_id', 'status', 'is_featured',
    ];

    protected $casts = ['is_featured' => 'boolean'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug');
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function posts(): HasMany { return $this->hasMany(Post::class)->orderBy('episode_number'); }
    public function episodes(): HasMany { return $this->posts(); }
    public function follows(): HasMany { return $this->hasMany(Follow::class); }

    public function getEpisodeCountAttribute(): int
    {
        return $this->posts()->count();
    }
}
