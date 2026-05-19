<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitLog extends Model
{
    // Esto permite que el controlador guarde los datos
    protected $fillable = ['habit_id', 'completed_at'];

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}
