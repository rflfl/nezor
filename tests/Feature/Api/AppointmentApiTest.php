<?php

namespace Tests\Feature\Api;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Professional;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Customer $customer;
    protected Professional $professional;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->customer = Customer::factory()->create(['user_id' => $this->user->id]);
        $this->professional = Professional::factory()->create(['user_id' => $this->user->id]);
        $this->service = Service::factory()->create([
            'user_id' => $this->user->id,
            'price' => 100,
            'professional_percentage' => 40,
            'salon_percentage' => 60,
            'duration_minutes' => 60,
        ]);
    }

    // test
    public function test_lists_appointments_for_authenticated_user()
    {
        Appointment::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/appointments');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    // test
    public function test_creates_an_appointment_with_auto_calculated_commission()
    {
        $data = [
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'appointment_date' => today()->format('Y-m-d'),
            'starts_at' => '10:00',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/appointments', $data);

        $response->assertCreated()
            ->assertJsonFragment([
                'service_price' => '100.00',
                'professional_amount' => '40.00',
                'salon_amount' => '60.00',
                'status' => 'scheduled',
            ]);

        $this->assertDatabaseHas('appointments', [
            'customer_id' => $this->customer->id,
            'service_price' => 100,
            'professional_amount' => 40,
            'salon_amount' => 60,
        ]);
    }

    // test
    public function test_calculates_ends_at_based_on_service_duration()
    {
        $data = [
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'appointment_date' => today()->format('Y-m-d'),
            'starts_at' => '10:00',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/appointments', $data);

        $response->assertCreated()
            ->assertJsonFragment(['ends_at' => '11:00']);
    }

    // test
    public function test_filters_appointments_by_date()
    {
        $today = today()->format('Y-m-d');
        $yesterday = today()->subDay()->format('Y-m-d');

        Appointment::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'appointment_date' => $today,
        ]);

        Appointment::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'appointment_date' => $yesterday,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/appointments?date={$today}");

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    // test
    public function test_updates_appointment_status()
    {
        $appointment = Appointment::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/appointments/{$appointment->id}", [
                'status' => 'completed',
            ]);

        $response->assertOk()
            ->assertJsonFragment(['status' => 'completed']);
    }

    // test
    public function test_rejects_invalid_status()
    {
        $appointment = Appointment::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/appointments/{$appointment->id}", [
                'status' => 'invalid_status',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    // test
    public function test_recalculates_commission_when_service_changes()
    {
        $newService = Service::factory()->create([
            'user_id' => $this->user->id,
            'price' => 200,
            'professional_percentage' => 50,
            'salon_percentage' => 50,
        ]);

        $appointment = Appointment::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'service_price' => 100,
            'professional_amount' => 40,
            'salon_amount' => 60,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/appointments/{$appointment->id}", [
                'service_id' => $newService->id,
            ]);

        $response->assertOk()
            ->assertJsonFragment([
                'service_price' => '200.00',
                'professional_amount' => '100.00',
                'salon_amount' => '100.00',
            ]);
    }

    // test
    public function test_deletes_an_appointment()
    {
        $appointment = Appointment::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
        ]);

        $this->actingAs($this->user)
            ->deleteJson("/api/appointments/{$appointment->id}")
            ->assertNoContent();

        $this->assertSoftDeleted('appointments', ['id' => $appointment->id]);
    }
}
