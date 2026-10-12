<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KontenWarta extends Model
{
    protected $table = 'konten_warta';

    protected $fillable = [
        'user_id',
        'kategori',
        'judul',
        'slug',
        'isi',
        'gambar',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (KontenWarta $konten) {
            if (empty($konten->slug)) {
                $konten->slug = Str::slug($konten->judul).'-'.Str::random(5);
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * URL publik gambar warta, atau null bila tidak ada gambar.
     */
    public function gambarUrl(): ?string
    {
        if (! $this->gambar) {
            return null;
        }

        return Storage::disk('public')->url($this->gambar);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
