<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReadingHistory extends Model
{
    protected $table = 'user_reading_history'; // Set nama tabel secara eksplisit

    protected $fillable = [
        'user_id',
        'pasal_id',
        'waktu_baca',
        'selesai_baca',
    ];

    protected $casts = [
        'waktu_baca' => 'datetime',
        'selesai_baca' => 'boolean',
    ];

    // Relationship: History milik satu user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship: History untuk satu pasal
    public function pasal(): BelongsTo
    {
        return $this->belongsTo(Pasal::class, 'pasal_id');
    }

    // Scope untuk user tertentu
    public function scopeUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope untuk yang selesai dibaca
    public function scopeSelesai($query)
    {
        return $query->where('selesai_baca', true);
    }
}
