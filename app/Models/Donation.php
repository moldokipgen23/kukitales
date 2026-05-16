<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    protected $fillable = [
        'campaign_id', 'user_id',
        'donor_name', 'donor_email', 'donor_phone',
        'amount', 'currency', 'payment_method', 'payment_reference',
        'status', 'message', 'is_anonymous', 'show_on_wall',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'show_on_wall' => 'boolean',
    ];

    public function campaign(): BelongsTo { return $this->belongsTo(DonationCampaign::class, 'campaign_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function getDisplayNameAttribute(): string
    {
        return $this->is_anonymous ? 'Anonymous' : $this->donor_name;
    }

    protected static function booted(): void
    {
        // Recalculate campaign raised_amount whenever a donation status changes
        static::saved(function (Donation $d) {
            if ($d->wasChanged('status') || $d->wasRecentlyCreated) {
                $d->campaign?->recalculateRaised();
            }
        });

        static::deleted(function (Donation $d) {
            $d->campaign?->recalculateRaised();
        });
    }
}
