<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoutineRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'exercises' => 'required|array|min:1',
            'exercises.*.exercise_id' => 'required|exists:exercises,id',
            'exercises.*.sequence' => 'required|integer|min:1|distinct',
            'exercises.*.target_sets' => 'required|integer|min:1|max:20',
            'exercises.*.target_reps' => 'required|integer|min:1|max:100',
            'exercises.*.rest_seconds' => 'required|integer|min:0|max:600',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la rutina es obligatorio',
            'name.string' => 'El nombre debe ser texto',
            'name.max' => 'El nombre no puede superar los 255 caracteres',
            
            'exercises.required' => 'Debes incluir al menos un ejercicio',
            'exercises.array' => 'Los ejercicios deben ser un array',
            'exercises.min' => 'Debes incluir al menos un ejercicio',
            
            'exercises.*.exercise_id.required' => 'El ID del ejercicio es obligatorio',
            'exercises.*.exercise_id.exists' => 'El ejercicio seleccionado no existe en la base de datos',
            
            'exercises.*.sequence.required' => 'La secuencia es obligatoria',
            'exercises.*.sequence.distinct' => 'No puede haber ejercicios con la misma secuencia',
            
            'exercises.*.target_sets.required' => 'El número de series es obligatorio',
            'exercises.*.target_sets.min' => 'Debe haber al menos 1 serie',
            'exercises.*.target_sets.max' => 'No puede haber más de 20 series',
            
            'exercises.*.target_reps.required' => 'El número de repeticiones es obligatorio',
            'exercises.*.target_reps.min' => 'Debe haber al menos 1 repetición',
            'exercises.*.target_reps.max' => 'No puede haber más de 100 repeticiones',
            
            'exercises.*.rest_seconds.required' => 'El tiempo de descanso es obligatorio',
            'exercises.*.rest_seconds.min' => 'El tiempo de descanso no puede ser negativo',
            'exercises.*.rest_seconds.max' => 'El tiempo de descanso no puede superar los 10 minutos',
        ];
    }
}