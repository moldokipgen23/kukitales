<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class DonationCampaign extends Model
{
    use HasSlug;

    protected $fillable = [
        'title', 'slug', 'short_description', 'description', 'cover_image',
        'goal_amount', 'raised_amount', 'currency', 'beneficiary',
        'is_active', 'is_featured', 'starts_at', 'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'goal_amount' => 'decimal:2',
        'raised_amount' => 'decimal:2',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'campaign_id');
    }

    public function confirmedDonations(): HasMany
    {
        return $this->donations()->where('status', 'confirmed');
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    }

    public function getProgressPercentAttribute(): float
    {
        if (! $this->goal_amount || $this->goal_amount <= 0) return 0;
        return min(100, round(($this->raised_amount / $this->goal_amount) * 100, 1));
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    public function scopeFeatured(Builder $q): Builder
    {
        return $q->where('is_featured', true);
    }

    /**
     * Recalculate raised_amount from confirmed donations.
     */
    public function recalculateRaised(): void
    {
        $total = $this->donations()->where('status', 'confirmed')->sum('amount');
        $this->update(['raised_amount' => $total]);
    }
}
