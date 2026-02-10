<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exercise_routine', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
            $table->foreignId('routine_id')->constrained()->onDelete('cascade');
            
            // Atributos extra del pivot
            $table->integer('sequence'); // Orden del ejercicio en la rutina
            $table->integer('target_sets'); // Series objetivo
            $table->integer('target_reps'); // Repeticiones objetivo
            $table->integer('rest_seconds'); // Tiempo de descanso en segundos
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_routine');
    }
};
