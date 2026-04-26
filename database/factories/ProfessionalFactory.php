<?php

namespace Database\Factories;

use App\Models\Professional;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfessionalFactory extends Factory
{
    protected $model = Professional::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'phone' => fake()->optional(0.7)->numerify('(##) #####-####'),
            'email' => fake()->optional(0.5)->safeEmail(),
            'default_commission_percentage' => fake()->randomElement([30, 35, 40, 45, 50]),
            'active' => true,
        ];
    }
}
