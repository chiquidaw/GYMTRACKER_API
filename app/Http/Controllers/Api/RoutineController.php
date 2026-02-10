<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Routine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoutineController extends Controller
{
    /**
     * GET /api/routines
     * Devuelve las rutinas asociadas al usuario autenticado
     */
    public function index(Request $request)
    {
        // Obtener las rutinas del usuario autenticado con sus ejercicios y categorías
        $routines = $request->user()
            ->routines()
            ->with(['exercises.category'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $routines
        ]);
    }

    /**
     * POST /api/routines
     * Crea una rutina, la asocia al usuario y añade los ejercicios
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exercises' => 'required|array|min:1',
            'exercises.*.exercise_id' => 'required|exists:exercises,id',
            'exercises.*.sequence' => 'required|integer|min:1',
            'exercises.*.target_sets' => 'required|integer|min:1',
            'exercises.*.target_reps' => 'required|integer|min:1',
            'exercises.*.rest_seconds' => 'required|integer|min:0',
        ]);

        try {
            // Usar transacción para asegurar integridad de datos
            DB::beginTransaction();

            // 1. Crear la rutina
            $routine = Routine::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            // 2. Asociar la rutina al usuario autenticado
            $request->user()->routines()->attach($routine->id);

            // 3. Asociar los ejercicios con sus datos del pivot
            foreach ($validated['exercises'] as $exerciseData) {
                $routine->exercises()->attach($exerciseData['exercise_id'], [
                    'sequence' => $exerciseData['sequence'],
                    'target_sets' => $exerciseData['target_sets'],
                    'target_reps' => $exerciseData['target_reps'],
                    'rest_seconds' => $exerciseData['rest_seconds'],
                ]);
            }

            DB::commit();

            // Cargar las relaciones para la respuesta
            $routine->load('exercises.category');

            return response()->json([
                'success' => true,
                'message' => 'Rutina creada exitosamente',
                'data' => $routine
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la rutina',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/routines/{id}
     * Muestra una rutina específica del usuario autenticado
     */
    public function show(Request $request, $id)
    {
        // Verificar que la rutina pertenezca al usuario
        $routine = $request->user()
            ->routines()
            ->with(['exercises.category'])
            ->find($id);

        if (!$routine) {
            return response()->json([
                'success' => false,
                'message' => 'Rutina no encontrada o no autorizada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $routine
        ]);
    }

    /**
     * PUT/PATCH /api/routines/{id}
     * Actualiza una rutina del usuario autenticado
     */
    public function update(Request $request, $id)
    {
        // Verificar que la rutina pertenezca al usuario
        $routine = $request->user()->routines()->find($id);

        if (!$routine) {
            return response()->json([
                'success' => false,
                'message' => 'Rutina no encontrada o no autorizada'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'exercises' => 'sometimes|required|array|min:1',
            'exercises.*.exercise_id' => 'required|exists:exercises,id',
            'exercises.*.sequence' => 'required|integer|min:1',
            'exercises.*.target_sets' => 'required|integer|min:1',
            'exercises.*.target_reps' => 'required|integer|min:1',
            'exercises.*.rest_seconds' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Actualizar datos básicos
            $routine->update([
                'name' => $validated['name'] ?? $routine->name,
                'description' => $validated['description'] ?? $routine->description,
            ]);

            // Si se enviaron ejercicios, actualizar la relación
            if (isset($validated['exercises'])) {
                // Eliminar ejercicios anteriores
                $routine->exercises()->detach();

                // Añadir los nuevos ejercicios
                foreach ($validated['exercises'] as $exerciseData) {
                    $routine->exercises()->attach($exerciseData['exercise_id'], [
                        'sequence' => $exerciseData['sequence'],
                        'target_sets' => $exerciseData['target_sets'],
                        'target_reps' => $exerciseData['target_reps'],
                        'rest_seconds' => $exerciseData['rest_seconds'],
                    ]);
                }
            }

            DB::commit();

            $routine->load('exercises.category');

            return response()->json([
                'success' => true,
                'message' => 'Rutina actualizada exitosamente',
                'data' => $routine
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la rutina',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /api/routines/{id}
     * Elimina la suscripción del usuario a una rutina
     */
    public function destroy(Request $request, $id)
    {
        // Verificar que la rutina pertenezca al usuario
        $routine = $request->user()->routines()->find($id);

        if (!$routine) {
            return response()->json([
                'success' => false,
                'message' => 'Rutina no encontrada o no autorizada'
            ], 404);
        }

        // Desuscribir al usuario de la rutina
        $request->user()->routines()->detach($id);

        return response()->json([
            'success' => true,
            'message' => 'Suscripción a rutina eliminada exitosamente'
        ]);
    }
}