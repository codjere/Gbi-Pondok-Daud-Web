<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoolGroup extends Model
{
    protected $fillable = [
        'nama', 'pemimpin', 'kecamatan', 'lokasi', 'latitude', 'longitude',
        'jadwal', 'min_usia', 'max_usia', 'tema_minat', 'kapasitas', 'terisi',
    ];

    protected function casts(): array
    {
        return [
            'tema_minat' => 'array',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function choices(): HasMany
    {
        return $this->hasMany(UserCoolChoice::class);
    }

    public function sisaKuota(): int
    {
        return max(0, $this->kapasitas - $this->terisi);
    }
}
