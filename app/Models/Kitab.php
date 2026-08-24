<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kitab extends Model
{
    protected $table = 'kitab'; // Set nama tabel secara eksplisit

    protected $fillable = [
        'nama',
        'singkatan',
        'urutan',
        'jumlah_pasal',
        'testament',
    ];

    // Relationship: Satu kitab memiliki banyak pasal
    public function pasal(): HasMany
    {
        return $this->hasMany(Pasal::class);
    }

    // Scope untuk Perjanjian Lama
    public function scopePerjanjianLama($query)
    {
        return $query->where('testament', 'PL');
    }

    // Scope untuk Perjanjian Baru
    public function scopePerjanjianBaru($query)
    {
        return $query->where('testament', 'PB');
    }

    // Scope untuk urutan
    public function scopeUrutan($query)
    {
        return $query->orderBy('urutan');
    }
}
