<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingPlanItem extends Model
{
    protected $table = 'reading_plan_items'; // Set nama tabel secara eksplisit

    protected $fillable = [
        'reading_plan_id',
        'hari_ke',
        'bacaan',
    ];

    protected $casts = [
        'bacaan' => 'array', // Cast JSON field to array
    ];

    // Relationship: Item milik satu reading plan
    public function readingPlan(): BelongsTo
    {
        return $this->belongsTo(ReadingPlan::class, 'reading_plan_id');
    }

    // Helper untuk mendapatkan deskripsi bacaan hari ini
    public function deskripsiBacaan(): string
    {
        $deskripsi = [];
        foreach ($this->bacaan as $item) {
            $kitab = Kitab::find($item['kitab_id']);
            if ($kitab) {
                if ($item['pasal_mulai'] == $item['pasal_selesai']) {
                    $deskripsi[] = $kitab->singkatan . ' ' . $item['pasal_mulai'];
                } else {
                    $deskripsi[] = $kitab->singkatan . ' ' . $item['pasal_mulai'] . '-' . $item['pasal_selesai'];
                }
            }
        }
        return implode(', ', $deskripsi);
    }
}
