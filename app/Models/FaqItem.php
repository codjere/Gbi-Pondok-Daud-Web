<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqItem extends Model
{
    protected $fillable = ['kategori', 'pertanyaan', 'jawaban', 'keywords'];

    protected function casts(): array
    {
        return [
            'keywords' => 'array',
        ];
    }
}
