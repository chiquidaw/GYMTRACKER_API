<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\RoutineController;
use Illuminate\Support\Facades\Route;

// Rutas públicas (sin autenticación)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas (requieren autenticación)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Categorías
    Route::apiResource('categories', CategoryController::class);
    
    // Ejercicios
    Route::apiResource('exercises', ExerciseController::class);

    // Rutinas
    Route::apiResource('routines', RoutineController::class);
});
