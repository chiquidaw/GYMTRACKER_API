<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddExerciseToRoutineRequest;
use App\Http\Requests\StoreRoutineRequest;
use App\Http\Requests\UpdateRoutineRequest;
use App\Models\Routine;
use App\Http\Resources\RoutineResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function store(StoreRoutineRequest $request)
    {
        $validated = $request->validated();
        
        try {
            DB::beginTransaction();

            $routine = Routine::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            foreach ($validated['exercises'] as $exerciseData) {
                $routine->exercises()->attach($exerciseData['exercise_id'], [
                    'sequence' => $exerciseData['sequence'],
                    'target_sets' => $exerciseData['target_sets'],
                    'target_reps' => $exerciseData['target_reps'],
                    'rest_seconds' => $exerciseData['rest_seconds'],
                ]);
            }

            DB::commit();
            
            $routine->load('exercises.category');

            return (new RoutineResource($routine))
                ->additional(['message' => 'Rutina creada exitosamente'])
                ->response()
                ->setStatusCode(201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la rutina',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateRoutineRequest $request, $id)
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

    public function addExercise(AddExerciseToRoutineRequest $request, $id)
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