<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
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
            'instruction' => $this->instruction,
            'category' => new CategoryResource($this->whenLoaded('category')),
            
            // Datos del pivot cuando el ejercicio viene de una rutina
            'sequence' => $this->when($this->pivot, $this->pivot?->sequence),
            'target_sets' => $this->when($this->pivot, $this->pivot?->target_sets),
            'target_reps' => $this->when($this->pivot, $this->pivot?->target_reps),
            'rest_seconds' => $this->when($this->pivot, $this->pivot?->rest_seconds),
            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}