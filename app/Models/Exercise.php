<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'instruction',
    ];

    /**
     * Relación N:1 con Category
     * Un ejercicio pertenece a una categoría
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relación N:M con Routine
     * Un ejercicio puede estar en muchas rutinas
     */
    public function routines()
    {
        return $this->belongsToMany(Routine::class, 'exercise_routine')
                    ->withPivot('sequence', 'target_sets', 'target_reps', 'rest_seconds')
                    ->withTimestamps()
                    ->orderBy('sequence');
    }
}
