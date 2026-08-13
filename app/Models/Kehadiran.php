<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kehadiran extends Model
{
    protected $table = 'kehadiran';

    protected $fillable = [
        'user_id',
        'jadwal_ibadah_id',
        'waktu_checkin',
        'metode_verifikasi',
    ];

    protected function casts(): array
    {
        return [
            'waktu_checkin' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jadwalIbadah(): BelongsTo
    {
        return $this->belongsTo(JadwalIbadah::class);
    }
}
