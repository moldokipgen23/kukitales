<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = ['post_id', 'file_path', 'file_type', 'file_size', 'alt_text', 'sort_order'];

    public function post(): BelongsTo { return $this->belongsTo(Post::class); }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
