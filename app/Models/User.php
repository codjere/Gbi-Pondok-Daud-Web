<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'role',
        'face_embedding',
        'has_biometric',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'face_embedding',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'face_embedding' => 'array',
            'has_biometric' => 'boolean',
        ];
    }

    public function isPengurus(): bool
    {
        return $this->role === 'pengurus';
    }

    public function isJemaat(): bool
    {
        return $this->role === 'jemaat';
    }

    public function isPelayan(): bool
    {
        return $this->role === 'pelayan';
    }

    public function reservasi(): HasMany
    {
        return $this->hasMany(Reservasi::class);
    }

    public function permohonanDoa(): HasMany
    {
        return $this->hasMany(PermohonanDoa::class);
    }

    public function kontenWarta(): HasMany
    {
        return $this->hasMany(KontenWarta::class);
    }

    public function kehadiran(): HasMany
    {
        return $this->hasMany(Kehadiran::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function pelayananChoices(): HasMany
    {
        return $this->hasMany(UserPelayananChoice::class);
    }

    public function coolChoices(): HasMany
    {
        return $this->hasMany(UserCoolChoice::class);
    }

    public function chatConversations(): HasMany
    {
        return $this->hasMany(ChatConversation::class);
    }

    public function readingProgress(): HasMany
    {
        return $this->hasMany(UserReadingProgress::class);
    }

    public function readingHistory(): HasMany
    {
        return $this->hasMany(UserReadingHistory::class);
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));

        return strtoupper(
            collect($parts)->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('')
        );
    }

    /**
     * URL publik foto profil, atau null bila belum punya (pakai inisial sebagai fallback).
     */
    public function avatarUrl(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->avatar);
    }
}
