<?php

namespace App\Http\Controllers;

use App\Models\HabitLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::now()->format('Y-m-d');

        $hora = Carbon::now()->hour; // Captura la hora actual (0-23)

        if ($hora >= 6 && $hora < 12) {
            $saludo = 'Buenos días';
        } elseif ($hora >= 12 && $hora < 20) {
            $saludo = 'Buenas tardes';
        } else {
            $saludo = 'Buenas noches';
        }

        // Array con los 10 mensajes motivacionales sobre hábitos
        $frasesMotivacionales = [
            'El éxito es la suma de pequeños esfuerzos repetidos día tras día.',
            'Tu disciplina de hoy determina tu libertad de mañana.',
            'No rompas la cadena. La constancia es tu superpoder.',
            'Pequeños hábitos, grandes resultados. ¡Sigue así!',
            'La motivación te pone en marcha, el hábito te mantiene firme.',
            "Cada check de hoy es un regalo para tu 'yo' del futuro.",
            'No busques perfección, busca constancia. ¡Vamos a por el día!',
            'La disciplina es el puente entre tus metas y tus logros.',
            'Un día o el día uno. Tú decides el rumbo de hoy.',
            'Progreso lento es mejor que ningún progreso. Mantén el enfoque.',
        ];

        // Seleccionamos una frase aleatoria del array
        $fraseDelDia = $frasesMotivacionales[array_rand($frasesMotivacionales)];

        // Obtener y preparar la colección de hábitos con su estado
        $habits = $user->habits->map(function ($habit) use ($today) {
            // Evaluamos de golpe si el hábito ya se completó hoy
            $habit->done_today = $habit->logs()->where('completed_at', $today)->exists();
            // Calculamos su racha actual
            $habit->current_streak = $habit->getCurrentStreak();

            return $habit;
        });

        // Conteo de estadísticas
        $totalHabitsCount = $habits->count();
        $hechosHoyCount = $habits->where('done_today', true)->count();
        $porcentajeProgreso = $totalHabitsCount > 0 ? round(($hechosHoyCount / $totalHabitsCount) * 100) : 0;
        $mejorRacha = $habits->pluck('current_streak')->max() ?? 0;

        $user_name = explode(' ', $user->name)[0];

        //  Obtener logs para el mapa de calor ( 28 días)
        $activityLogs = HabitLog::with('habit')
            ->whereIn('habit_id', $user->habits->pluck('id'))
            ->where('completed_at', '>=', now()->subDays(28))
            ->get()
            ->groupBy('completed_at');

        // Lógica del mapa de calor procesada en el backend
        $heatmapDays = [];
        for ($i = 27; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->format('Y-m-d');

            // Obtenemos los logs de esta fecha específica
            $dayLogs = isset($activityLogs[$dateString]) ? $activityLogs[$dateString] : collect();
            $count = $dayLogs->count();

            // NUEVO: Extraemos los nombres de los hábitos hechos hoy y los unimos con comas
            if ($count > 0) {
                $nombresHabitos = $dayLogs->map(function ($log) {
                    return $log->habit->name; // Accedemos al nombre del hábito
                })->implode(', ');

                $tooltipTitle = $date->locale('es')->isoFormat('LL')." ($count): ".$nombresHabitos;
            } else {
                $tooltipTitle = $date->locale('es')->isoFormat('LL').': Ningún hábito completado';
            }

            // Determinar nivel de intensidad CSS
            $level = '';
            if ($count > 0) {
                if ($count >= $totalHabitsCount) {
                    $level = 'heat-high';
                } elseif ($count > $totalHabitsCount / 2) {
                    $level = 'heat-medium';
                } else {
                    $level = 'heat-low';
                }
            }
            $heatmapDays[] = [
                'dayNumber' => $date->format('j'),
                'levelClass' => $level,
                'tooltipTitle' => $tooltipTitle, // <--- Título ultra-detallado inyectado aquí
            ];
        }
        // Mostramos la fecha
        $date = now()->locale('es')->isoFormat('dddd, D [de] MMMM');

        // Enviamos la nueva variable limpia $habits a la vista
        return view('dashboard', compact(
            'saludo',
            'date',
            'habits',
            'totalHabitsCount',
            'hechosHoyCount',
            'porcentajeProgreso',
            'mejorRacha',
            'user_name',
            'fraseDelDia',
            'heatmapDays'
            
        ));
    }
}
