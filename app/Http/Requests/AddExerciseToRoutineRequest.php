<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddExerciseToRoutineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exercise_id' => 'required|exists:exercises,id',
            'sets' => 'required|integer|min:1|max:20',
            'reps' => 'required|integer|min:1|max:100',
            'rest_seconds' => 'nullable|integer|min:0|max:600',
        ];
    }

    public function messages(): array
    {
        return [
            'exercise_id.required' => 'El ID del ejercicio es obligatorio',
            'exercise_id.exists' => 'El ejercicio seleccionado no existe en la base de datos',
            'sets.required' => 'El número de series es obligatorio',
            'reps.required' => 'El número de repeticiones es obligatorio',
        ];
    }
}