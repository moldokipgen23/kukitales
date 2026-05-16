<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'role', 'avatar', 'bio', 'location', 'is_active',
        'provider', 'provider_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['admin', 'editor']) && $this->is_active;
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isEditor(): bool { return $this->role === 'editor'; }
    public function isAuthor(): bool { return $this->role === 'author'; }

    public function posts(): HasMany { return $this->hasMany(Post::class); }
    public function comments(): HasMany { return $this->hasMany(Comment::class); }
    public function bookmarks(): HasMany { return $this->hasMany(Bookmark::class); }
    public function follows(): HasMany { return $this->hasMany(Follow::class); }
    public function series(): HasMany { return $this->hasMany(Series::class); }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?background=c0392b&color=fff&name=' . urlencode($this->name);
    }
}
