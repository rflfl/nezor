<?php

namespace Database\Factories;

use App\Models\CashRegister;
use App\Models\Customer;
use App\Models\DailyServiceEntry;
use App\Models\Professional;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DailyServiceEntryFactory extends Factory
{
    protected $model = DailyServiceEntry::class;

    public function definition(): array
    {
        $grossAmount = fake()->randomFloat(2, 30, 300);
        $professionalPercentage = fake()->randomElement([30, 35, 40, 45, 50]);
        $professionalAmount = round($grossAmount * $professionalPercentage / 100, 2);
        $salonAmount = $grossAmount - $professionalAmount;
        $paymentStatus = fake()->randomElement(['pending', 'paid']);

        return [
            'user_id' => User::factory(),
            'customer_id' => Customer::factory(),
            'professional_id' => Professional::factory(),
            'service_id' => Service::factory(),
            'cash_register_id' => $paymentStatus === 'paid' ? CashRegister::factory() : null,
            'appointment_id' => null,
            'service_date' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'gross_amount' => $grossAmount,
            'professional_percentage' => $professionalPercentage,
            'professional_amount' => $professionalAmount,
            'salon_amount' => $salonAmount,
            'payment_status' => $paymentStatus,
            'payment_method' => $paymentStatus === 'paid' ? fake()->randomElement(['cash', 'pix', 'card', 'mixed']) : null,
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }
}
