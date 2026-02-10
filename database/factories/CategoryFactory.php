<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Pecho',
                'Espalda',
                'Piernas',
                'Hombros',
                'Brazos',
                'Abdomen',
                'Cardio',
                'Funcional'
            ]),
            'icon_path' => '/icons/' . fake()->slug() . '.svg',
        ];
    }
}