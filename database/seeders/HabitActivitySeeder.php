<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Habit;
use App\Models\HabitLog;
use Carbon\Carbon;

class HabitActivitySeeder extends Seeder
{
    public function run(): void
    {
        $habits = Habit::all();

        if ($habits->isEmpty()) {
            $this->command->info('Primero crea algunos hábitos en la web para poder generar actividad.');
            return;
        }

        // Generamos datos para los últimos 28 días
        for ($i = 0; $i < 28; $i++) {
            $fecha = Carbon::now()->subDays($i);

            foreach ($habits as $habit) {
                // Probabilidad del 70% de que haya cumplido el hábito ese día
                if (rand(1, 100) <= 70) {
                    HabitLog::create([
                        'habit_id' => $habit->id,
                        'completed_at' => $fecha->format('Y-m-d'),
                    ]);
                }
            }
        }
    }
}
