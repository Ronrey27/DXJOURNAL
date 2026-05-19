<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class HabitController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string',
        ]);

        $habit = Habit::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'icon' => $validated['icon'],
            'color' => '#764ba2',
        ]);

        // Si la petición es por AJAX, respondemos con éxito
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => '¡Hábito añadido correctamente!',
            ]);
        }

        return back()->with('success', '¡Hábito añadido!');
    }

    // Método para la Marcar como completado hoy
    public function check(Habit $habit)
    {
        $this->authorize('update', $habit);
        $today = now()->format('Y-m-d');

        // USAMOS EL MODELO (HabitLog) para que devuelva un objeto Eloquent con el método delete()
        $log = HabitLog::where('habit_id', $habit->id)
            ->where('completed_at', $today)
            ->first();

        if ($log) {
            $log->delete(); // Ahora sí funcionará porque es un Modelo Eloquent
            $status = 'unchecked';
        } else {
            HabitLog::create([
                'habit_id' => $habit->id,
                'completed_at' => $today,
            ]);
            $status = 'checked';
        }

        if (request()->ajax()) {
            return response()->json([
                'status' => $status,
                'message' => $status === 'checked' ? '¡Hábito completado!' : 'Hábito desmarcado.',
            ]);
        }

        return back();
    }

    // Eliminar
    public function destroy(Habit $habit) // <-- Asegúrate de que ponga 'Habit $habit'
    {
        $this->authorize('delete', $habit);

        $habit->delete(); // Si $habit es un modelo real, este método existe siempre

        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Hábito eliminado correctamente.',
            ]);
        }

        return back()->with('success', 'Hábito eliminado correctamente');
    }


    // Actualizar
    public function update(Request $request, Habit $habit)
    {
        // Capa de protección: Solo el creador del hábito puede editarlo
        $this->authorize('update', $habit);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
        ]);

        $habit->update($validated);

        // Si viene por JavaScript, respondemos en JSON
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => '¡Hábito actualizado con éxito!',
            ]);
        }

        return redirect()->route('dashboard')->with('success', '¡Hábito actualizado!');
    }
}
