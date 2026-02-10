<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoutineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            
            // Lista de ejercicios con datos del pivot al mismo nivel
            'exercises' => ExerciseResource::collection($this->whenLoaded('exercises')),
            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}