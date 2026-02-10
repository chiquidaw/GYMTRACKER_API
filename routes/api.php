<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\RoutineController;

// ==================== RUTAS PÚBLICAS ====================

// Autenticación
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Categories (Público)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/categories/{id}/exercises', [CategoryController::class, 'exercises']);

// Exercises (Público)
Route::get('/exercises', [ExerciseController::class, 'index']);
Route::get('/exercises/{id}', [ExerciseController::class, 'show']);

// Routines (Público - Rutinas disponibles para todos)
Route::get('/routines', [RoutineController::class, 'index']);
Route::get('/routines/{id}', [RoutineController::class, 'show']);
Route::get('/routines/{id}/exercises', [RoutineController::class, 'exercises']);

// ==================== RUTAS PROTEGIDAS (Token) ====================

Route::middleware('auth:sanctum')->group(function () {
    
    // Autenticación
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Categories
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    
    // Exercises 
    Route::post('/exercises', [ExerciseController::class, 'store']);
    Route::put('/exercises/{id}', [ExerciseController::class, 'update']);
    Route::delete('/exercises/{id}', [ExerciseController::class, 'destroy']);
    
    // Routines
    Route::post('/routines', [RoutineController::class, 'store']);
    Route::put('/routines/{id}', [RoutineController::class, 'update']);
    Route::delete('/routines/{id}', [RoutineController::class, 'destroy']);
    Route::post('/routines/{id}/exercises', [RoutineController::class, 'addExercise']);
    Route::delete('/routines/{id}/exercises/{exercise_id}', [RoutineController::class, 'removeExercise']);
    
    // My Routines (Suscripciones del usuario)
    Route::get('/my-routines', [RoutineController::class, 'myRoutines']);
    Route::post('/my-routines', [RoutineController::class, 'subscribe']);
    Route::delete('/my-routines/{id}', [RoutineController::class, 'unsubscribe']);
});