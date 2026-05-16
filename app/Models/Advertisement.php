<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = [
        'title', 'type', 'placement',
        'image_path', 'video_url', 'custom_html',
        'link_url', 'alt_text', 'label',
        'is_active', 'priority',
        'starts_at', 'ends_at',
        'view_count', 'click_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    public function scopeForPlacement(Builder $q, string $placement): Builder
    {
        return $q->where('placement', $placement)->active()->orderByDesc('priority')->orderBy('id');
    }

    /**
     * Render this ad to HTML — call from Blade.
     */
    public function render(): string
    {
        // Track view (fire-and-forget)
        $this->incrementOrTouch('view_count');

        $label = $this->label ? '<div class="ad-label">' . e($this->label) . '</div>' : '';
        $click = $this->link_url
            ? route('ads.click', $this) // tracking redirect
            : null;

        $inner = '';
        switch ($this->type) {
            case 'banner_image':
                if (! $this->image_url) return '';
                $img = '<img src="' . e($this->image_url) . '" alt="' . e($this->alt_text ?: $this->title) . '" loading="lazy" style="width:100%;height:auto;display:block;">';
                $inner = $click ? '<a href="' . e($click) . '" target="_blank" rel="noopener sponsored">' . $img . '</a>' : $img;
                break;

            case 'video':
                if (! $this->video_url) return '';
                // Detect YouTube/Vimeo and embed, else assume mp4
                if (preg_match('~(youtube\.com|youtu\.be)~', $this->video_url)) {
                    $embed = $this->youtubeEmbedUrl($this->video_url);
                    $inner = '<div class="ad-video"><iframe src="' . e($embed) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
                } elseif (str_contains($this->video_url, 'vimeo.com')) {
                    $vid = preg_replace('~.*/(\d+).*~', '$1', parse_url($this->video_url, PHP_URL_PATH));
                    $inner = '<div class="ad-video"><iframe src="https://player.vimeo.com/video/' . e($vid) . '" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe></div>';
                } else {
                    $inner = '<div class="ad-video"><video src="' . e($this->video_url) . '" controls preload="metadata" playsinline style="width:100%;height:auto;"></video></div>';
                }
                break;

            case 'custom_html':
                // Trusted source (admin) — render raw
                $inner = '<div class="ad-html">' . $this->custom_html . '</div>';
                break;
        }

        if (! $inner) return '';

        return '<aside class="ad-slot ad-' . e($this->placement) . '" data-ad-id="' . $this->id . '">'
            . $label
            . $inner
            . '</aside>';
    }

    protected function youtubeEmbedUrl(string $url): string
    {
        // Convert youtu.be/X or watch?v=X to embed/X
        if (preg_match('~(?:youtu\.be/|v=)([A-Za-z0-9_-]{6,})~', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1] . '?rel=0';
        }
        return $url;
    }

    protected function incrementOrTouch(string $col): void
    {
        try {
            \DB::table($this->getTable())->where('id', $this->id)->increment($col);
        } catch (\Throwable $e) {
            // silent — never break the page over analytics
        }
    }
}
