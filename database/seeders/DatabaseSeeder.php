<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\CashRegister;
use App\Models\Customer;
use App\Models\DailyServiceEntry;
use App\Models\Professional;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Criar usuario demo
        $owner = User::factory()->create([
            'name' => 'Proprietaria Demo',
            'email' => 'owner@nezor.com',
            'password' => Hash::make('password'),
        ]);

        $this->command->info('Usuario criado: owner@nezor.com / password');

        // Criar clientes
        $customers = Customer::factory()->count(15)->create([
            'user_id' => $owner->id,
        ]);
        $this->command->info("{$customers->count()} clientes criados");

        // Criar profissionais
        $professionals = Professional::factory()->count(5)->create([
            'user_id' => $owner->id,
        ]);
        $this->command->info("{$professionals->count()} profissionais criados");

        // Criar servicos
        $servicesData = [
            ['name' => 'Corte Feminino', 'category' => 'Cabelo', 'duration_minutes' => 60, 'price' => 80, 'professional_percentage' => 40],
            ['name' => 'Corte Masculino', 'category' => 'Cabelo', 'duration_minutes' => 30, 'price' => 50, 'professional_percentage' => 40],
            ['name' => 'Coloração', 'category' => 'Cabelo', 'duration_minutes' => 120, 'price' => 200, 'professional_percentage' => 35],
            ['name' => 'Mechas', 'category' => 'Cabelo', 'duration_minutes' => 150, 'price' => 250, 'professional_percentage' => 35],
            ['name' => 'Escova', 'category' => 'Cabelo', 'duration_minutes' => 45, 'price' => 60, 'professional_percentage' => 40],
            ['name' => 'Hidratação', 'category' => 'Cabelo', 'duration_minutes' => 60, 'price' => 90, 'professional_percentage' => 40],
            ['name' => 'Progressiva', 'category' => 'Cabelo', 'duration_minutes' => 180, 'price' => 300, 'professional_percentage' => 30],
            ['name' => 'Manicure', 'category' => 'Unhas', 'duration_minutes' => 45, 'price' => 45, 'professional_percentage' => 50],
            ['name' => 'Pedicure', 'category' => 'Unhas', 'duration_minutes' => 60, 'price' => 55, 'professional_percentage' => 50],
            ['name' => 'Massagem Relaxante', 'category' => 'Estética', 'duration_minutes' => 60, 'price' => 120, 'professional_percentage' => 45],
            ['name' => 'Limpeza de Pele', 'category' => 'Estética', 'duration_minutes' => 90, 'price' => 150, 'professional_percentage' => 40],
            ['name' => 'Design de Sobrancelhas', 'category' => 'Estética', 'duration_minutes' => 30, 'price' => 40, 'professional_percentage' => 50],
        ];

        $services = collect();
        foreach ($servicesData as $svc) {
            $services->push(Service::factory()->create(array_merge($svc, [
                'user_id' => $owner->id,
                'salon_percentage' => 100 - $svc['professional_percentage'],
            ])));
        }
        $this->command->info("{$services->count()} servicos criados");

        // Criar agendamentos
        $today = Carbon::today();
        $appointments = collect();

        for ($i = -20; $i <= 20; $i++) {
            $date = $today->copy()->addDays($i);
            $numAppts = rand(0, 4);

            for ($j = 0; $j < $numAppts; $j++) {
                $service = $services->random();
                $professional = $professionals->random();
                $customer = $customers->random();
                $hour = rand(8, 18);
                $minute = rand(0, 1) * 30;
                $startsAt = sprintf('%02d:%02d', $hour, $minute);
                $endsAt = sprintf('%02d:%02d', $hour + intdiv($service->duration_minutes, 60), $minute);

                $status = $date->isFuture() || $date->isToday()
                    ? fake()->randomElement(['scheduled', 'confirmed', 'in_progress'])
                    : fake()->randomElement(['completed', 'cancelled', 'no_show']);

                $professionalAmount = round($service->price * $service->professional_percentage / 100, 2);

                $appointments->push(Appointment::factory()->create([
                    'user_id' => $owner->id,
                    'customer_id' => $customer->id,
                    'professional_id' => $professional->id,
                    'service_id' => $service->id,
                    'appointment_date' => $date->format('Y-m-d'),
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'status' => $status,
                    'service_price' => $service->price,
                    'professional_percentage' => $service->professional_percentage,
                    'professional_amount' => $professionalAmount,
                    'salon_amount' => $service->price - $professionalAmount,
                ]));
            }
        }
        $this->command->info("{$appointments->count()} agendamentos criados");

        // Criar caixas
        $cashRegisters = collect();
        for ($i = -15; $i <= 0; $i++) {
            $date = $today->copy()->addDays($i);
            $status = $i === 0 ? 'open' : (rand(0, 10) > 2 ? 'closed' : 'open');
            $openingAmount = fake()->randomFloat(2, 100, 300);
            $expectedAmount = fake()->randomFloat(2, 500, 3000);

            $cashRegisters->push(CashRegister::factory()->create([
                'user_id' => $owner->id,
                'opened_by' => $owner->id,
                'closed_by' => $status === 'closed' ? $owner->id : null,
                'open_date' => $date->format('Y-m-d'),
                'open_time' => '08:00:00',
                'opening_amount' => $openingAmount,
                'expected_amount' => $expectedAmount,
                'counted_amount' => $status === 'closed' ? $expectedAmount + fake()->randomFloat(2, -30, 30) : null,
                'difference_amount' => $status === 'closed' ? fake()->randomFloat(2, -30, 30) : 0,
                'status' => $status,
                'closing_note' => $status === 'closed' && rand(0, 10) > 7 ? 'Diferenca no fechamento' : null,
            ]));
        }
        $this->command->info("{$cashRegisters->count()} caixas criados");

        // Criar lancamentos diarios
        $entries = collect();
        for ($i = -25; $i <= 0; $i++) {
            $date = $today->copy()->addDays($i);
            $numEntries = rand(2, 8);
            $cashRegister = $cashRegisters->firstWhere('open_date', $date->format('Y-m-d'));

            for ($j = 0; $j < $numEntries; $j++) {
                $service = $services->random();
                $professional = $professionals->random();
                $customer = $customers->random();
                $paymentStatus = fake()->randomElement(['pending', 'paid']);
                $grossAmount = $service->price + fake()->randomFloat(2, -10, 20);
                $professionalAmount = round($grossAmount * $service->professional_percentage / 100, 2);

                $entries->push(DailyServiceEntry::factory()->create([
                    'user_id' => $owner->id,
                    'customer_id' => $customer->id,
                    'professional_id' => $professional->id,
                    'service_id' => $service->id,
                    'cash_register_id' => ($paymentStatus === 'paid' && $cashRegister) ? $cashRegister->id : null,
                    'service_date' => $date->format('Y-m-d'),
                    'gross_amount' => $grossAmount,
                    'professional_percentage' => $service->professional_percentage,
                    'professional_amount' => $professionalAmount,
                    'salon_amount' => $grossAmount - $professionalAmount,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $paymentStatus === 'paid' ? fake()->randomElement(['cash', 'pix', 'card', 'mixed']) : null,
                ]));
            }
        }
        $this->command->info("{$entries->count()} lancamentos diarios criados");

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('  SEED CONCLUIDO COM SUCESSO!');
        $this->command->info('========================================');
        $this->command->info('Login: owner@nezor.com');
        $this->command->info('Senha: password');
        $this->command->info('========================================');
    }
}
