<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Exercise;
use App\Models\Routine;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear usuarios de prueba
        $users = User::factory(5)->create();
        
        // Crear un usuario específico para pruebas
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // 2. Crear categorías musculares específicas
        $categories = [
            ['name' => 'Pecho', 'icon_path' => '/icons/chest.svg'],
            ['name' => 'Espalda', 'icon_path' => '/icons/back.svg'],
            ['name' => 'Piernas', 'icon_path' => '/icons/legs.svg'],
            ['name' => 'Hombros', 'icon_path' => '/icons/shoulders.svg'],
            ['name' => 'Brazos', 'icon_path' => '/icons/arms.svg'],
            ['name' => 'Abdomen', 'icon_path' => '/icons/abs.svg'],
            ['name' => 'Cardio', 'icon_path' => '/icons/cardio.svg'],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create($categoryData);
            
            // 3. Crear ejercicios para cada categoría
            $exercisesData = $this->getExercisesForCategory($category->name);
            
            foreach ($exercisesData as $exerciseData) {
                Exercise::create([
                    'category_id' => $category->id,
                    'name' => $exerciseData['name'],
                    'instruction' => $exerciseData['instruction'],
                ]);
            }
        }

        // 4. Crear rutinas con lógica compleja
        $allExercises = Exercise::all();
        $allUsers = User::all();

        for ($i = 0; $i < 10; $i++) {
            // Crear la rutina
            $routine = Routine::create([
                'name' => fake()->sentence(3),
                'description' => fake()->paragraph(),
            ]);

            // Asignar la rutina a varios usuarios aleatorios (2-4 usuarios)
            $randomUsers = $allUsers->random(rand(2, 4));
            $routine->users()->attach($randomUsers->pluck('id'));

            // Asignar ejercicios aleatorios con datos del pivot
            $randomExercises = $allExercises->random(rand(4, 8));
            
            $sequence = 1;
            foreach ($randomExercises as $exercise) {
                $routine->exercises()->attach($exercise->id, [
                    'sequence' => $sequence++,
                    'target_sets' => rand(2, 5),
                    'target_reps' => rand(8, 15),
                    'rest_seconds' => rand(30, 120),
                ]);
            }
        }
    }

    /**
     * Obtener ejercicios específicos para cada categoría
     */
    private function getExercisesForCategory(string $categoryName): array
    {
        $exercises = [
            'Pecho' => [
                ['name' => 'Press de Banca', 'instruction' => 'Acostado en banco plano, bajar la barra hasta el pecho y empujar hacia arriba.'],
                ['name' => 'Flexiones', 'instruction' => 'En posición de plancha, bajar el cuerpo flexionando los codos.'],
                ['name' => 'Aperturas con Mancuernas', 'instruction' => 'Tumbado en banco, abrir los brazos con mancuernas.'],
                ['name' => 'Press Inclinado', 'instruction' => 'En banco inclinado, empujar la barra hacia arriba.'],
            ],
            'Espalda' => [
                ['name' => 'Dominadas', 'instruction' => 'Colgado de la barra, elevar el cuerpo hasta que la barbilla supere la barra.'],
                ['name' => 'Remo con Barra', 'instruction' => 'Inclinado, tirar de la barra hacia el abdomen.'],
                ['name' => 'Peso Muerto', 'instruction' => 'Con la barra en el suelo, levantarla manteniendo la espalda recta.'],
                ['name' => 'Jalón al Pecho', 'instruction' => 'Sentado, tirar de la barra hacia el pecho.'],
            ],
            'Piernas' => [
                ['name' => 'Sentadillas', 'instruction' => 'Con barra en los hombros, bajar flexionando las rodillas.'],
                ['name' => 'Prensa de Piernas', 'instruction' => 'Empujar la plataforma con los pies.'],
                ['name' => 'Zancadas', 'instruction' => 'Dar un paso largo y bajar la rodilla trasera.'],
                ['name' => 'Curl Femoral', 'instruction' => 'Acostado boca abajo, flexionar las piernas llevando los talones hacia los glúteos.'],
            ],
            'Hombros' => [
                ['name' => 'Press Militar', 'instruction' => 'De pie, empujar la barra desde los hombros hacia arriba.'],
                ['name' => 'Elevaciones Laterales', 'instruction' => 'Elevar los brazos lateralmente con mancuernas.'],
                ['name' => 'Pájaros', 'instruction' => 'Inclinado, elevar los brazos lateralmente.'],
                ['name' => 'Press Arnold', 'instruction' => 'Rotar las muñecas mientras se empujan las mancuernas hacia arriba.'],
            ],
            'Brazos' => [
                ['name' => 'Curl de Bíceps', 'instruction' => 'Flexionar los codos levantando las mancuernas.'],
                ['name' => 'Fondos en Paralelas', 'instruction' => 'Bajar y subir el cuerpo entre dos barras paralelas.'],
                ['name' => 'Extensiones de Tríceps', 'instruction' => 'Extender los brazos por encima de la cabeza.'],
                ['name' => 'Curl Martillo', 'instruction' => 'Curl con mancuernas en posición neutra.'],
            ],
            'Abdomen' => [
                ['name' => 'Crunches', 'instruction' => 'Acostado, elevar el torso hacia las rodillas.'],
                ['name' => 'Plancha', 'instruction' => 'Mantener el cuerpo recto apoyado en antebrazos y pies.'],
                ['name' => 'Elevaciones de Piernas', 'instruction' => 'Acostado, elevar las piernas hasta formar 90 grados.'],
                ['name' => 'Russian Twist', 'instruction' => 'Sentado, rotar el torso de lado a lado.'],
            ],
            'Cardio' => [
                ['name' => 'Burpees', 'instruction' => 'Combinación de flexión, salto y sentadilla.'],
                ['name' => 'Saltos de Cuerda', 'instruction' => 'Saltar mientras se gira la cuerda.'],
                ['name' => 'Mountain Climbers', 'instruction' => 'En posición de plancha, llevar las rodillas al pecho alternadamente.'],
                ['name' => 'Jumping Jacks', 'instruction' => 'Saltar abriendo y cerrando piernas y brazos.'],
            ],
        ];

        return $exercises[$categoryName] ?? [];
    }
}