<?php

namespace Tests\Feature\Api;

use App\Models\CashRegister;
use App\Models\Customer;
use App\Models\DailyServiceEntry;
use App\Models\Professional;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyEntryApiTest extends TestCase
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
        ]);
    }

    // test
    public function test_lists_daily_entries()
    {
        DailyServiceEntry::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/daily-service-entries');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    // test
    public function test_creates_a_paid_entry_linked_to_open_cash_register()
    {
        $cashRegister = CashRegister::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'open',
            'expected_amount' => 0,
        ]);

        $data = [
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'service_date' => today()->format('Y-m-d'),
            'gross_amount' => 100.00,
            'professional_percentage' => 40,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/daily-service-entries', $data);

        $response->assertCreated()
            ->assertJsonFragment([
                'gross_amount' => '100.00',
                'professional_amount' => '40.00',
                'salon_amount' => '60.00',
                'payment_status' => 'paid',
            ]);

        $this->assertDatabaseHas('daily_service_entries', [
            'cash_register_id' => $cashRegister->id,
            'gross_amount' => 100.00,
        ]);

        $cashRegister->refresh();
        $this->assertEquals(60.00, $cashRegister->expected_amount);
    }

    // test
    public function test_creates_a_pending_entry_without_cash_register_link()
    {
        $data = [
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'service_date' => today()->format('Y-m-d'),
            'gross_amount' => 100.00,
            'professional_percentage' => 40,
            'payment_status' => 'pending',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/daily-service-entries', $data);

        $response->assertCreated()
            ->assertJsonFragment([
                'payment_status' => 'pending',
            ])
            ->assertJsonPath('cash_register_id', null);
    }

    // test
    public function test_rejects_paid_entry_without_open_cash_register()
    {
        $data = [
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'service_date' => today()->format('Y-m-d'),
            'gross_amount' => 100.00,
            'professional_percentage' => 40,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/daily-service-entries', $data);

        $response->assertUnprocessable()
            ->assertJsonFragment(['message' => 'Não há caixa aberto. Abra o caixa primeiro.']);
    }

    // test
    public function test_calculates_commission_correctly()
    {
        $data = [
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'service_date' => today()->format('Y-m-d'),
            'gross_amount' => 150.00,
            'professional_percentage' => 35,
            'payment_status' => 'pending',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/daily-service-entries', $data);

        $response->assertCreated()
            ->assertJsonFragment([
                'professional_amount' => '52.50',
                'salon_amount' => '97.50',
            ]);
    }

    // test
    public function test_updates_payment_status_to_paid_and_links_cash_register()
    {
        $cashRegister = CashRegister::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'open',
            'expected_amount' => 0,
        ]);

        $entry = DailyServiceEntry::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'payment_status' => 'pending',
            'cash_register_id' => null,
            'salon_amount' => 60,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/daily-service-entries/{$entry->id}", [
                'payment_status' => 'paid',
                'payment_method' => 'pix',
            ]);

        $response->assertOk()
            ->assertJsonFragment([
                'payment_status' => 'paid',
                'cash_register_id' => $cashRegister->id,
            ]);
    }

    // test
    public function test_rejects_changing_to_paid_without_open_cash_register()
    {
        $entry = DailyServiceEntry::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'payment_status' => 'pending',
            'cash_register_id' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/daily-service-entries/{$entry->id}", [
                'payment_status' => 'paid',
            ]);

        $response->assertUnprocessable()
            ->assertJsonFragment(['message' => 'Não há caixa aberto']);
    }

    // test
    public function test_updates_gross_amount_and_recalculates_commission()
    {
        $entry = DailyServiceEntry::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'gross_amount' => 100,
            'professional_percentage' => 40,
            'professional_amount' => 40,
            'salon_amount' => 60,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/daily-service-entries/{$entry->id}", [
                'gross_amount' => 200.00,
            ]);

        $response->assertOk()
            ->assertJsonFragment([
                'gross_amount' => '200.00',
                'professional_amount' => '80.00',
                'salon_amount' => '120.00',
            ]);
    }

    // test
    public function test_deletes_entry_and_adjusts_cash_register()
    {
        $cashRegister = CashRegister::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'open',
            'expected_amount' => 100,
        ]);

        $entry = DailyServiceEntry::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'cash_register_id' => $cashRegister->id,
            'payment_status' => 'paid',
            'salon_amount' => 60,
        ]);

        $this->actingAs($this->user)
            ->deleteJson("/api/daily-service-entries/{$entry->id}")
            ->assertNoContent();

        $this->assertSoftDeleted('daily_service_entries', ['id' => $entry->id]);

        $cashRegister->refresh();
        $this->assertEquals(40.00, $cashRegister->expected_amount);
    }

    // test
    public function test_filters_entries_by_date()
    {
        $today = today()->format('Y-m-d');

        DailyServiceEntry::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'service_date' => $today,
            'payment_status' => 'paid',
        ]);

        DailyServiceEntry::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'service_date' => today()->subDay()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/daily-service-entries?today=1');

        $response->assertOk();
        $this->assertGreaterThanOrEqual(1, count($response->json('data')));
    }

    // test
    public function test_filters_entries_by_payment_status()
    {
        DailyServiceEntry::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'payment_status' => 'paid',
        ]);

        DailyServiceEntry::factory()->create([
            'user_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'professional_id' => $this->professional->id,
            'service_id' => $this->service->id,
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/daily-service-entries?payment_status=paid');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }
}
