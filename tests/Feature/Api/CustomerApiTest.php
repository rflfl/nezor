<?php

namespace Tests\Feature\Api;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // test
    public function test_lists_customers_for_authenticated_user()
    {
        Customer::factory()->count(3)->create(['user_id' => $this->user->id]);
        Customer::factory()->create(); // different user

        $response = $this->actingAs($this->user)
            ->getJson('/api/customers');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    // test
    public function test_requires_authentication()
    {
        $this->getJson('/api/customers')->assertUnauthorized();
    }

    // test
    public function test_creates_a_customer()
    {
        $data = [
            'name' => 'Maria Silva',
            'phone' => '11999998888',
            'email' => 'maria@example.com',
            'birth_date' => '1990-05-15',
            'notes' => 'Cliente VIP',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/customers', $data);

        $response->assertCreated()
            ->assertJsonFragment(['name' => 'Maria Silva']);

        $this->assertDatabaseHas('customers', [
            'name' => 'Maria Silva',
            'phone' => '11999998888',
            'user_id' => $this->user->id,
        ]);
    }

    // test
    public function test_requires_name_and_phone_to_create()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/customers', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'phone']);
    }

    // test
    public function test_shows_a_customer()
    {
        $customer = Customer::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/customers/{$customer->id}");

        $response->assertOk()
            ->assertJsonFragment(['name' => $customer->name]);
    }

    // test
    public function test_prevents_showing_other_users_customers()
    {
        $otherCustomer = Customer::factory()->create();

        $this->actingAs($this->user)
            ->getJson("/api/customers/{$otherCustomer->id}")
            ->assertForbidden();
    }

    // test
    public function test_updates_a_customer()
    {
        $customer = Customer::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/customers/{$customer->id}", [
                'name' => 'Maria Souza',
                'phone' => '11888887777',
            ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Maria Souza']);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Maria Souza',
        ]);
    }

    // test
    public function test_deletes_a_customer()
    {
        $customer = Customer::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->deleteJson("/api/customers/{$customer->id}")
            ->assertNoContent();

        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }

    // test
    public function test_searches_customers_by_name()
    {
        Customer::factory()->create(['user_id' => $this->user->id, 'name' => 'Ana Paula']);
        Customer::factory()->create(['user_id' => $this->user->id, 'name' => 'Carlos']);

        $response = $this->actingAs($this->user)
            ->getJson('/api/customers?search=Ana');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name');
        $this->assertContains('Ana Paula', $names->toArray());
        $this->assertNotContains('Carlos', $names->toArray());
    }

    // test
    public function test_filters_customers_by_active_status()
    {
        Customer::factory()->create(['user_id' => $this->user->id, 'active' => true]);
        Customer::factory()->create(['user_id' => $this->user->id, 'active' => false]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/customers?active=1');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }
}
