<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id', 'usia', 'jenis_kelamin', 'kecamatan', 'alamat',
        'minat', 'bakat', 'ketersediaan_hari', 'ketersediaan_waktu', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'minat' => 'array',
            'bakat' => 'array',
            'ketersediaan_hari' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isComplete(): bool
    {
        return $this->completed_at !== null;
    }
}
