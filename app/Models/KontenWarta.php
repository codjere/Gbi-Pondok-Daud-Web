<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
