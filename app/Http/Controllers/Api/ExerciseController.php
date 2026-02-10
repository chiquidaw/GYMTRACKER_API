<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    /**
     * GET /api/exercises
     * Lista todos los ejercicios con sus categorías
     * Permite filtrar por categoría mediante query string
     */
    public function index(Request $request)
    {
        $query = Exercise::with('category');

        // Filtrar por categoría si se proporciona el parámetro
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Búsqueda por nombre
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $exercises = $query->get();

        return response()->json([
            'success' => true,
            'data' => $exercises
        ]);
    }

    /**
     * POST /api/exercises
     * Crea un nuevo ejercicio
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'instruction' => 'required|string',
        ]);

        $exercise = Exercise::create($validated);
        $exercise->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Ejercicio creado exitosamente',
            'data' => $exercise
        ], 201);
    }

    /**
     * GET /api/exercises/{id}
     * Muestra un ejercicio específico
     */
    public function show($id)
    {
        $exercise = Exercise::with('category')->find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Ejercicio no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $exercise
        ]);
    }

    /**
     * PUT/PATCH /api/exercises/{id}
     * Actualiza un ejercicio
     */
    public function update(Request $request, $id)
    {
        $exercise = Exercise::find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Ejercicio no encontrado'
            ], 404);
        }

        $validated = $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'name' => 'sometimes|required|string|max:255',
            'instruction' => 'sometimes|required|string',
        ]);

        $exercise->update($validated);
        $exercise->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Ejercicio actualizado exitosamente',
            'data' => $exercise
        ]);
    }

    /**
     * DELETE /api/exercises/{id}
     * Elimina un ejercicio
     */
    public function destroy($id)
    {
        $exercise = Exercise::find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Ejercicio no encontrado'
            ], 404);
        }

        // Verificar si está asociado a rutinas
        if ($exercise->routines()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el ejercicio porque está asociado a rutinas'
            ], 409);
        }

        $exercise->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ejercicio eliminado exitosamente'
        ]);
    }
}