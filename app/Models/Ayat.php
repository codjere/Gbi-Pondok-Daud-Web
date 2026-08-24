<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ayat extends Model
{
    protected $table = 'ayat'; // Set nama tabel secara eksplisit

    protected $fillable = [
        'pasal_id',
        'nomor_ayat',
        'isi_ayat',
    ];

    // Relationship: Ayat milik satu pasal
    public function pasal(): BelongsTo
    {
        return $this->belongsTo(Pasal::class);
    }

    // Scope untuk nomor ayat
    public function scopeNomor($query, $nomor)
    {
        return $query->where('nomor_ayat', $nomor);
    }

    // Helper untuk referensi lengkap (contoh: "Kejadian 1:1")
    public function referensi(): string
    {
        return $this->pasal->kitab->singkatan . ' ' . $this->pasal->nomor_pasal . ':' . $this->nomor_ayat;
    }
}
