<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Routine;
use App\Http\Resources\RoutineResource;
use Illuminate\Http\Request;

class RoutineController extends Controller
{
    /**
     * GET /api/routines
     */
    public function index()
    {
        $routines = Routine::with(['exercises.category'])->get();

        return RoutineResource::collection($routines);
    }

    /**
     * GET /api/routines/{id}
     */
    public function show($id)
    {
        $routine = Routine::with(['exercises.category'])->find($id);

        if (!$routine) {
            return response()->json([
                'success' => false,
                'message' => 'Rutina no encontrada'
            ], 404);
        }

        return new RoutineResource($routine);
    }

    /**
     * GET /api/routines/{id}/exercises
     */
    public function exercises($id)
    {
        $routine = Routine::find($id);

        if (!$routine) {
            return response()->json([
                'success' => false,
                'message' => 'Rutina no encontrada'
            ], 404);
        }

        $exercises = $routine->exercises()->with('category')->get();

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\ExerciseResource::collection($exercises)
        ]);
    }

    // ... resto de métodos igual pero cambiando las respuestas:
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $routine = Routine::create($validated);

        return (new RoutineResource($routine))
            ->additional(['message' => 'Rutina creada exitosamente'])
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, $id)
    {
        $routine = Routine::find($id);

        if (!$routine) {
            return response()->json([
                'success' => false,
                'message' => 'Rutina no encontrada'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $routine->update($validated);

        return (new RoutineResource($routine))
            ->additional(['message' => 'Rutina actualizada exitosamente']);
    }

    public function destroy($id)
    {
        $routine = Routine::find($id);

        if (!$routine) {
            return response()->json([
                'success' => false,
                'message' => 'Rutina no encontrada'
            ], 404);
        }

        $routine->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rutina eliminada exitosamente'
        ]);
    }

    public function addExercise(Request $request, $id)
    {
        $routine = Routine::find($id);

        if (!$routine) {
            return response()->json([
                'success' => false,
                'message' => 'Rutina no encontrada'
            ], 404);
        }

        $validated = $request->validate([
            'exercise_id' => 'required|exists:exercises,id',
            'reps' => 'required|integer|min:1',
            'sets' => 'required|integer|min:1',
        ]);

        $maxSequence = $routine->exercises()->max('sequence') ?? 0;

        $routine->exercises()->attach($validated['exercise_id'], [
            'sequence' => $maxSequence + 1,
            'target_sets' => $validated['sets'],
            'target_reps' => $validated['reps'],
            'rest_seconds' => $request->input('rest_seconds', 60),
        ]);

        $routine->load('exercises.category');

        return (new RoutineResource($routine))
            ->additional(['message' => 'Ejercicio añadido a la rutina exitosamente']);
    }

    public function removeExercise($routineId, $exerciseId)
    {
        $routine = Routine::find($routineId);

        if (!$routine) {
            return response()->json([
                'success' => false,
                'message' => 'Rutina no encontrada'
            ], 404);
        }

        $routine->exercises()->detach($exerciseId);

        return response()->json([
            'success' => true,
            'message' => 'Ejercicio eliminado de la rutina exitosamente'
        ]);
    }

    public function myRoutines(Request $request)
    {
        $routines = $request->user()
            ->routines()
            ->with(['exercises.category'])
            ->get();

        return RoutineResource::collection($routines);
    }

    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'routine_id' => 'required|exists:routines,id',
        ]);

        $user = $request->user();

        if ($user->routines()->where('routine_id', $validated['routine_id'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Ya estás suscrito a esta rutina'
            ], 409);
        }

        $user->routines()->attach($validated['routine_id']);

        return response()->json([
            'success' => true,
            'message' => 'Suscripción exitosa a la rutina'
        ]);
    }

    public function unsubscribe(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->routines()->where('routine_id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No estás suscrito a esta rutina'
            ], 404);
        }

        $user->routines()->detach($id);

        return response()->json([
            'success' => true,
            'message' => 'Desuscripción exitosa de la rutina'
        ]);
    }
}