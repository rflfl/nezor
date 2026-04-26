<?php

namespace Tests\Feature\Api;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // test
    public function test_lists_services_for_authenticated_user()
    {
        Service::factory()->count(3)->create(['user_id' => $this->user->id]);
        Service::factory()->create(); // different user

        $response = $this->actingAs($this->user)
            ->getJson('/api/services');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    // test
    public function test_creates_a_service_with_valid_percentages()
    {
        $data = [
            'name' => 'Corte Feminino',
            'category' => 'Cabelo',
            'duration_minutes' => 60,
            'price' => 80.00,
            'professional_percentage' => 40,
            'salon_percentage' => 60,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/services', $data);

        $response->assertCreated()
            ->assertJsonFragment(['name' => 'Corte Feminino']);

        $this->assertDatabaseHas('services', [
            'name' => 'Corte Feminino',
            'user_id' => $this->user->id,
        ]);
    }

    // test
    public function test_rejects_service_when_percentages_do_not_sum_to_100()
    {
        $data = [
            'name' => 'Corte',
            'duration_minutes' => 30,
            'price' => 50,
            'professional_percentage' => 30,
            'salon_percentage' => 60,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/services', $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['salon_percentage']);
    }

    // test
    public function test_rejects_service_with_invalid_duration()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/services', [
                'name' => 'Corte',
                'duration_minutes' => 3,
                'price' => 50,
                'professional_percentage' => 50,
                'salon_percentage' => 50,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['duration_minutes']);
    }

    // test
    public function test_updates_a_service()
    {
        $service = Service::factory()->create([
            'user_id' => $this->user->id,
            'professional_percentage' => 40,
            'salon_percentage' => 60,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/services/{$service->id}", [
                'name' => 'Corte Premium',
                'price' => 100,
            ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Corte Premium', 'price' => '100.00']);
    }

    // test
    public function test_rejects_update_when_new_percentages_do_not_sum_to_100()
    {
        $service = Service::factory()->create([
            'user_id' => $this->user->id,
            'professional_percentage' => 40,
            'salon_percentage' => 60,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/services/{$service->id}", [
                'professional_percentage' => 30,
                'salon_percentage' => 50,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['salon_percentage']);
    }

    // test
    public function test_deletes_a_service()
    {
        $service = Service::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->deleteJson("/api/services/{$service->id}")
            ->assertNoContent();

        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }

    // test
    public function test_filters_services_by_category()
    {
        Service::factory()->create(['user_id' => $this->user->id, 'category' => 'Cabelo']);
        Service::factory()->create(['user_id' => $this->user->id, 'category' => 'Unhas']);

        $response = $this->actingAs($this->user)
            ->getJson('/api/services?category=Cabelo');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    // test
    public function test_lists_categories()
    {
        Service::factory()->create(['user_id' => $this->user->id, 'category' => 'Cabelo']);
        Service::factory()->create(['user_id' => $this->user->id, 'category' => 'Unhas']);
        Service::factory()->create(['user_id' => $this->user->id, 'category' => null]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/services/categories');

        $response->assertOk();
        $categories = $response->json();
        $this->assertContains('Cabelo', $categories);
        $this->assertContains('Unhas', $categories);
        $this->assertNotContains(null, $categories);
    }
}
