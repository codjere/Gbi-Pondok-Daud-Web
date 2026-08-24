<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReadingProgress extends Model
{
    protected $table = 'user_reading_progress'; // Set nama tabel secara eksplisit

    protected $fillable = [
        'user_id',
        'reading_plan_id',
        'hari_terakhir',
        'tanggal_mulai',
        'tanggal_selesai',
        'selesai',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'selesai' => 'boolean',
    ];

    // Relationship: Progress milik satu user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship: Progress untuk satu reading plan
    public function readingPlan(): BelongsTo
    {
        return $this->belongsTo(ReadingPlan::class, 'reading_plan_id');
    }

    // Helper untuk hitung persentase progress
    public function persentaseProgress(): float
    {
        if ($this->selesai) {
            return 100.0;
        }

        $totalHari = $this->readingPlan->durasi_hari;
        if ($totalHari == 0) {
            return 0.0;
        }

        return round(($this->hari_terakhir / $totalHari) * 100, 1);
    }

    // Helper untuk status
    public function status(): string
    {
        if ($this->selesai) {
            return 'Selesai';
        } elseif ($this->hari_terakhir == 0) {
            return 'Belum Mulai';
        } else {
            return 'Sedang Berjalan';
        }
    }
}
