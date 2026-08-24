<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReadingPlan extends Model
{
    protected $table = 'reading_plans'; // Set nama tabel secara eksplisit

    protected $fillable = [
        'nama',
        'deskripsi',
        'durasi_hari',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    // Relationship: Satu plan memiliki banyak item bacaan
    public function items(): HasMany
    {
        return $this->hasMany(ReadingPlanItem::class, 'reading_plan_id')->orderBy('hari_ke');
    }

    // Relationship: Satu plan memiliki banyak progress user
    public function userProgress(): HasMany
    {
        return $this->hasMany(UserReadingProgress::class, 'reading_plan_id');
    }

    // Scope untuk plan yang aktif
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    // Helper untuk format durasi
    public function durasiFormatted(): string
    {
        if ($this->durasi_hari >= 365) {
            return $this->durasi_hari / 365 . ' Tahun';
        } elseif ($this->durasi_hari >= 30) {
            return round($this->durasi_hari / 30) . ' Bulan';
        }
        return $this->durasi_hari . ' Hari';
    }
}
