<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoutineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'exercises' => 'sometimes|array|min:1',
            'exercises.*.exercise_id' => 'required|exists:exercises,id',
            'exercises.*.sequence' => 'required|integer|min:1|distinct',
            'exercises.*.target_sets' => 'required|integer|min:1|max:20',
            'exercises.*.target_reps' => 'required|integer|min:1|max:100',
            'exercises.*.rest_seconds' => 'required|integer|min:0|max:600',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la rutina es obligatorio',
            'exercises.min' => 'Debes incluir al menos un ejercicio',
            'exercises.*.exercise_id.exists' => 'El ejercicio seleccionado no existe',
        ];
    }
}