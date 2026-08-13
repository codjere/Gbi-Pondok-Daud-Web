<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservasi extends Model
{
    protected $table = 'reservasi';

    protected $fillable = [
        'user_id',
        'jadwal_ibadah_id',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jadwalIbadah(): BelongsTo
    {
        return $this->belongsTo(JadwalIbadah::class);
    }
}
