<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $professionalPercentage = fake()->randomElement([30, 35, 40, 45, 50]);

        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement([
                'Corte Feminino', 'Corte Masculino', 'Coloração', 'Mechas',
                'Escova', 'Hidratação', 'Progressiva', 'Manicure', 'Pedicure',
                'Massagem Relaxante', 'Limpeza de Pele', 'Design de Sobrancelhas',
                'Depilação', 'Maquiagem', 'Penteado',
            ]),
            'category' => fake()->randomElement(['Cabelo', 'Unhas', 'Estética', 'Depilação', 'Maquiagem']),
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90, 120]),
            'price' => fake()->randomFloat(2, 30, 300),
            'professional_percentage' => $professionalPercentage,
            'salon_percentage' => 100 - $professionalPercentage,
            'active' => true,
        ];
    }
}
