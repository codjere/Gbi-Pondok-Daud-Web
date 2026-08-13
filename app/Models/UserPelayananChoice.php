<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPelayananChoice extends Model
{
    protected $fillable = ['user_id', 'tim_pelayanan_id', 'score', 'source'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function timPelayanan(): BelongsTo
    {
        return $this->belongsTo(TimPelayanan::class);
    }
}
