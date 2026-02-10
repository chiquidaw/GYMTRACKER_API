<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * GET /api/categories
     * Lista todas las categorías
     */
    public function index()
    {
        $categories = Category::withCount('exercises')->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * POST /api/categories
     * Crea una nueva categoría
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'icon_path' => 'required|string|max:255',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada exitosamente',
            'data' => $category
        ], 201);
    }

    /**
     * GET /api/categories/{id}
     * Muestra una categoría específica con sus ejercicios
     */
    public function show($id)
    {
        $category = Category::with('exercises')->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * PUT/PATCH /api/categories/{id}
     * Actualiza una categoría
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $id,
            'icon_path' => 'sometimes|required|string|max:255',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada exitosamente',
            'data' => $category
        ]);
    }

    /**
     * DELETE /api/categories/{id}
     * Elimina una categoría
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        // Verificar si tiene ejercicios asociados
        if ($category->exercises()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la categoría porque tiene ejercicios asociados'
            ], 409);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada exitosamente'
        ]);
    }

    /**
     * GET /api/categories/{id}/exercises
     * Lista los ejercicios de una categoría específica
     */
    public function exercises($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        $exercises = $category->exercises;

        return response()->json([
            'success' => true,
            'data' => $exercises
        ]);
    }
}