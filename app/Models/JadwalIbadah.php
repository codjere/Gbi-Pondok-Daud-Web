<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalIbadah extends Model
{
    protected $table = 'jadwal_ibadah';

    protected $fillable = [
        'nama_sesi',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'pembicara',
        'lokasi',
        'batas_kuota',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function jamMulaiFormatted(): string
    {
        return substr((string) $this->jam_mulai, 0, 5);
    }

    public function jamSelesaiFormatted(): string
    {
        return substr((string) $this->jam_selesai, 0, 5);
    }

    public function reservasi(): HasMany
    {
        return $this->hasMany(Reservasi::class);
    }

    public function kehadiran(): HasMany
    {
        return $this->hasMany(Kehadiran::class);
    }

    public function jumlahTerisi(): int
    {
        return $this->reservasi()->where('status', 'confirmed')->count();
    }

    public function sisaKuota(): int
    {
        return max(0, $this->batas_kuota - $this->jumlahTerisi());
    }

    public function isPenuh(): bool
    {
        return $this->sisaKuota() <= 0;
    }

    public function statusKuota(): string
    {
        if ($this->isPenuh()) {
            return 'penuh';
        }

        $pct = $this->batas_kuota > 0
            ? ($this->jumlahTerisi() / $this->batas_kuota) * 100
            : 0;

        return $pct >= 90 ? 'hampir' : 'tersedia';
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'terpublikasi')
            ->where('tanggal', '>=', now()->toDateString());
    }

    public function scopeTerbaru($query)
    {
        return $query->orderBy('tanggal')->orderBy('jam_mulai');
    }
}
