<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCoolChoice extends Model
{
    protected $fillable = ['user_id', 'cool_group_id', 'score', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coolGroup(): BelongsTo
    {
        return $this->belongsTo(CoolGroup::class);
    }
}
