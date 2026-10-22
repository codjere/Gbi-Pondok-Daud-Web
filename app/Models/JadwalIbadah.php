<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalIbadah extends Model
{
    protected $table = 'jadwal_ibadah';

    protected $fillable = [
        'nama_sesi',
        'deskripsi',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'pembicara',
        'lokasi',
        'meeting_link',
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

    /**
     * Morning Prayer: kegiatan doa harian tetap setiap jam 05:00 WITA.
     *
     * Dibuat sebagai objek virtual (tidak disimpan ke database) sehingga
     * selalu tampil di jadwal setiap hari tanpa perlu scheduler. Tanggal
     * di-set ke hari ini agar lolos filter scopeAktif. Ditandai is_daily
     * supaya tampilan tidak menampilkan opsi reservasi/kuota.
     */
    public static function morningPrayer(): self
    {
        $jadwal = new self([
            'nama_sesi' => 'Morning Prayer',
            'deskripsi' => 'Doa pagi bersama setiap hari secara online via Zoom. Terbuka untuk seluruh jemaat.',
            'tanggal' => now()->toDateString(),
            'jam_mulai' => '05:00',
            'jam_selesai' => '06:00',
            'pembicara' => 'Tim Pendoa GBI Pondok Daud',
            'lokasi' => 'Online (Zoom)',
            'meeting_link' => 'https://zoom.us/j/7628887157?pwd=VlM4TFp2dFI3S0ovcFdjUGxMYys2dz09',
            'batas_kuota' => 0,
            'status' => 'terpublikasi',
        ]);

        // Penanda bahwa ini kegiatan harian (bukan ibadah ber-kuota).
        $jadwal->is_daily = true;
        // Exists=false memastikan ini tidak dianggap tersimpan di DB.
        $jadwal->exists = false;

        return $jadwal;
    }
}
