<?php

namespace Database\Factories;

use App\Models\CashRegister;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CashRegisterFactory extends Factory
{
    protected $model = CashRegister::class;

    public function definition(): array
    {
        $openingAmount = fake()->randomFloat(2, 50, 200);
        $expectedAmount = fake()->randomFloat(2, 100, 2000);
        $countedAmount = $expectedAmount + fake()->randomFloat(2, -50, 50);
        $difference = $countedAmount - $expectedAmount;

        return [
            'user_id' => User::factory(),
            'opened_by' => User::factory(),
            'closed_by' => fake()->boolean(70) ? User::factory() : null,
            'open_date' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'open_time' => fake()->time('H:i:s'),
            'opening_amount' => $openingAmount,
            'expected_amount' => $expectedAmount,
            'counted_amount' => fake()->boolean(70) ? $countedAmount : null,
            'difference_amount' => fake()->boolean(70) ? $difference : 0,
            'status' => fake()->randomElement(['open', 'closed']),
            'closing_note' => fake()->optional(0.3)->sentence(),
        ];
    }
}
