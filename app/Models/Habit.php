<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    protected $fillable = ['user_id', 'name', 'icon', 'color'];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    public function getCurrentStreak()
    {
        $streak = 0;
        $today = now()->startOfDay();

        // Obtenemos todas las fechas de los logs, de la más nueva a la más vieja
        $logDates = $this->logs()
            ->orderBy('completed_at', 'desc')
            ->pluck('completed_at')
            ->map(fn ($date) => Carbon::parse($date)->startOfDay());

        if ($logDates->isEmpty()) {
            return 0;
        }

        // Si el último log no es de hoy ni de ayer, la racha es 0
        $lastLog = $logDates->first();
        if (! $lastLog->isToday() && ! $lastLog->isYesterday()) {
            return 0;
        }

        // Recorremos las fechas para ver cuántas son consecutivas
        $currentDate = $lastLog;
        foreach ($logDates as $logDate) {
            if ($logDate->equalTo($currentDate)) {
                $streak++;
                $currentDate->subDay();
            } elseif ($logDate->lessThan($currentDate)) {
                break; // Se rompió la racha
            }
        }

        return $streak;
    }
}
