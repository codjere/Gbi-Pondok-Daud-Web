<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasal extends Model
{
    protected $table = 'pasal'; // Set nama tabel secara eksplisit

    protected $fillable = [
        'kitab_id',
        'nomor_pasal',
        'jumlah_ayat',
    ];

    // Relationship: Pasal milik satu kitab
    public function kitab(): BelongsTo
    {
        return $this->belongsTo(Kitab::class);
    }

    // Relationship: Satu pasal memiliki banyak ayat
    public function ayat(): HasMany
    {
        return $this->hasMany(Ayat::class)->orderBy('nomor_ayat');
    }

    // Scope untuk nomor pasal
    public function scopeNomor($query, $nomor)
    {
        return $query->where('nomor_pasal', $nomor);
    }

    // Helper untuk nama lengkap (contoh: "Kejadian 1")
    public function namaLengkap(): string
    {
        return $this->kitab->nama . ' ' . $this->nomor_pasal;
    }
}
