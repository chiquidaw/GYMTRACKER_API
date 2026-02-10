<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relación N:M con User
     * Una rutina puede ser usada por muchos usuarios
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'routine_user')
                    ->withTimestamps();
    }

    /**
     * Relación N:M con Exercise
     * Una rutina puede tener muchos ejercicios
     * Incluye los datos extra del pivot: sequence, sets, reps, rest
     */
    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'exercise_routine')
                    ->withPivot('sequence', 'target_sets', 'target_reps', 'rest_seconds')
                    ->withTimestamps()
                    ->orderBy('sequence');
    }
}
