<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermohonanDoa extends Model
{
    protected $table = 'permohonan_doa';

    protected $fillable = [
        'user_id',
        'pokok_doa',
        'is_anonymous',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
