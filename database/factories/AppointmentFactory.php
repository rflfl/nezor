<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Professional;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $service = Service::inRandomOrder()->first() ?? Service::factory()->create();
        $professionalPercentage = $service->professional_percentage;
        $servicePrice = $service->price;
        $professionalAmount = round($servicePrice * $professionalPercentage / 100, 2);
        $salonAmount = $servicePrice - $professionalAmount;

        $hour = fake()->numberBetween(8, 18);
        $minute = fake()->randomElement(['00', '30']);
        $startsAt = sprintf('%02d:%s', $hour, $minute);
        $endsAt = sprintf('%02d:%s', $hour + intdiv($service->duration_minutes, 60), $minute);

        return [
            'user_id' => User::factory(),
            'customer_id' => Customer::factory(),
            'professional_id' => Professional::factory(),
            'service_id' => $service->id,
            'appointment_date' => fake()->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d'),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => fake()->randomElement(['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show']),
            'service_price' => $servicePrice,
            'professional_percentage' => $professionalPercentage,
            'professional_amount' => $professionalAmount,
            'salon_amount' => $salonAmount,
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }
}
