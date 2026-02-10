<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon_path',
    ];

    /**
     * Relación 1:N con Exercise
     * Una categoría puede tener muchos ejercicios
     */
    public function exercises()
    {
        return $this->hasMany(Exercise::class);
    }
}
