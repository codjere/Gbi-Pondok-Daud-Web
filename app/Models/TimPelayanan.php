<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimPelayanan extends Model
{
    protected $table = 'tim_pelayanan';

    protected $fillable = [
        'nama', 'slug', 'deskripsi', 'minat_tags', 'bakat_tags',
        'min_usia', 'max_usia', 'hari_pelayanan', 'icon',
    ];

    protected function casts(): array
    {
        return [
            'minat_tags' => 'array',
            'bakat_tags' => 'array',
            'hari_pelayanan' => 'array',
        ];
    }

    public function choices(): HasMany
    {
        return $this->hasMany(UserPelayananChoice::class);
    }
}
